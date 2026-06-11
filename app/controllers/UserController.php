<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use App\Core\AuthMiddleware;

class UserController extends Controller
{
    private Usuario $usuario;

    public function __construct()
    {
        $this->usuario = new Usuario();
    }

    // POST /users — publico, qualquer um pode se registrar
public function store(): void
{
    $body  = json_decode($this->request->getContent(), true);
    $nome  = $body['nome']  ?? null;
    $email = $body['email'] ?? null;
    $senha = $body['senha'] ?? null;

    if (!$nome || !$email || !$senha) {
        $this->json(['error' => 'nome, email e senha sao obrigatorios'], 422);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->json(['error' => 'E-mail invalido'], 422);
    }

    if ($this->usuario->getUserByEmail($email)) {
        $this->json(['error' => 'E-mail ja cadastrado'], 409);
    }

    try {
        $id = $this->usuario->createUser($nome, $email, $senha);
    } catch (\PDOException $e) {
        // fallback caso o email duplicado passe pela verificação acima
        $this->json(['error' => 'E-mail ja cadastrado'], 409);
    }

    if (!$id) {
        $this->json(['error' => 'Erro ao criar usuario'], 500);
    }

    $this->json([
        'message' => 'Usuario criado com sucesso',
        'data'    => ['id' => $id, 'nome' => $nome, 'email' => $email]
    ], 201);
}

    // GET /users
    public function index(): void
    {
        AuthMiddleware::handle();
        $this->json(['data' => $this->usuario->getAllUsers()]);
    }

    // GET /users/{id}
    public function show(string $id): void
    {
        AuthMiddleware::handle();

        $id = (int) $id;

        if ($id <= 0) {
            $this->json(['error' => 'ID invalido'], 422);
        }

        $usuario = $this->usuario->getUserById($id);

        if (!$usuario) {
            $this->json(['error' => 'Usuario nao encontrado'], 404);
        }

        $this->json(['data' => $usuario]);
    }

  // PUT /users/{id}
public function update(string $id): void
{
    $usuarioLogado = AuthMiddleware::handle();
    $id            = (int) $id;

    if ($id <= 0) {
        $this->json(['error' => 'ID invalido'], 422);
    }

    $usuario = $this->usuario->getUserById($id);
    if (!$usuario) {
        $this->json(['error' => 'Usuario nao encontrado'], 404);
    }

    $body = json_decode($this->request->getContent(), true);
    if (empty($body)) {
        $this->json(['error' => 'Nenhum dado enviado'], 422);
    }

    // Valida senha atual obrigatoriamente
    $senhaAtual = $body['senha_atual'] ?? null;
    if (!$senhaAtual) {
        $this->json(['error' => 'Senha atual e obrigatoria para editar'], 422);
    }

    $usuarioComSenha = $this->usuario->getUserByEmail($usuario['email']);
    if (!password_verify($senhaAtual, $usuarioComSenha['senha'])) {
        $this->json(['error' => 'Senha atual incorreta'], 403);
    }

    if (isset($body['email']) && !filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
        $this->json(['error' => 'E-mail invalido'], 422);
    }

    $atualizado = $this->usuario->updateUser($id, $body);
    if (!$atualizado) {
        $this->json(['error' => 'Nenhuma alteracao realizada'], 422);
    }

    $usuario = $this->usuario->getUserById($id);
    $this->json(['message' => 'Usuario atualizado com sucesso', 'data' => $usuario]);
}

// DELETE /users/{id}
public function destroy(string $id): void
{
    $usuarioLogado = AuthMiddleware::handle();
    $id            = (int) $id;

    if ($id <= 0) {
        $this->json(['error' => 'ID invalido'], 422);
    }

    // Apenas o próprio usuário pode deletar sua conta
    if ((int) $usuarioLogado['sub'] !== $id) {
        $this->json(['error' => 'Sem permissao para deletar este usuario'], 403);
    }

    if (!$this->usuario->getUserById($id)) {
        $this->json(['error' => 'Usuario nao encontrado'], 404);
    }

    $this->usuario->unassignTasks($id);
    $this->usuario->deleteUser($id);

    $this->json(['message' => 'Usuario removido com sucesso']);
}
}