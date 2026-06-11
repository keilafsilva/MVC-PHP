<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Tarefa;
use App\Core\AuthMiddleware;

class TaskController extends Controller
{
    private Tarefa $tarefa;

    public function __construct()
    {
        $this->tarefa = new Tarefa();
    }

    // POST /tasks
    public function store(): void
    {
        $usuarioLogado = AuthMiddleware::handle();
        $criadoPorId   = (int) $usuarioLogado['sub'];

        $body         = json_decode($this->request->getContent(), true);
        $titulo       = $body['titulo']        ?? null;
        $descricao    = $body['descricao']     ?? null;
        $atribuidoAId = $body['atribuido_a_id'] ?? null;

        if (!$titulo) {
            $this->json(['error' => 'titulo e obrigatorio'], 422);
        }

        $id = $this->tarefa->createTarefa($titulo, $descricao, $criadoPorId, $atribuidoAId);

        if (!$id) {
            $this->json(['error' => 'Erro ao criar tarefa'], 500);
        }

        $tarefa = $this->tarefa->getTarefaById($id);

        $this->json([
            'message' => 'Tarefa criada com sucesso',
            'data'    => $tarefa
        ], 201);
    }

    // GET /tasks
    public function index(): void
    {
        $assignedTo = $this->request->query->get('assignedTo');

        if ($assignedTo) {
            $tarefas = $this->tarefa->getTarefasByUsuario((int) $assignedTo);
        } else {
            $tarefas = $this->tarefa->getAllTarefas();
        }

        $this->json(['data' => $tarefas]);
    }

    // GET /tasks/{id}
    public function show(string $id): void
    {
        AuthMiddleware::handle();

        $id = (int) $id;

        if ($id <= 0) {
            $this->json(['error' => 'ID invalido'], 422);
        }

        $tarefa = $this->tarefa->getTarefaById($id);

        if (!$tarefa) {
            $this->json(['error' => 'Tarefa nao encontrada'], 404);
        }

        $this->json(['data' => $tarefa]);
    }

    // PUT /tasks/{id}
    public function update(string $id): void
    {
        AuthMiddleware::handle();

        $id = (int) $id;

        if ($id <= 0) {
            $this->json(['error' => 'ID invalido'], 422);
        }

        $tarefa = $this->tarefa->getTarefaById($id);

        if (!$tarefa) {
            $this->json(['error' => 'Tarefa nao encontrada'], 404);
        }

        $body = json_decode($this->request->getContent(), true);

        if (empty($body)) {
            $this->json(['error' => 'Nenhum dado enviado'], 422);
        }

        $atualizado = $this->tarefa->updateTarefa($id, $body);

        if (!$atualizado) {
            $this->json(['error' => 'Nenhuma alteracao realizada'], 422);
        }

        $tarefa = $this->tarefa->getTarefaById($id);

        $this->json([
            'message' => 'Tarefa atualizada com sucesso',
            'data'    => $tarefa
        ]);
    }

    // DELETE /tasks/{id}
    public function destroy(string $id): void
    {
        AuthMiddleware::handle();

        $id = (int) $id;

        if ($id <= 0) {
            $this->json(['error' => 'ID invalido'], 422);
        }

        $tarefa = $this->tarefa->getTarefaById($id);

        if (!$tarefa) {
            $this->json(['error' => 'Tarefa nao encontrada'], 404);
        }

        $this->tarefa->deleteTarefa($id);

        $this->json(['message' => 'Tarefa removida com sucesso']);
    }
}
