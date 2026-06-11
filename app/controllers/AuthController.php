<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    private Usuario $usuario;
    private string $secret;

    public function __construct()
    {
        $this->usuario = new Usuario();
        $this->secret  = $_ENV['JWT_SECRET'] ?? 'secret';
    }

    // POST /auth/login
    public function login(): void
    {
        $body  = json_decode($this->request->getContent(), true);
        $email = $body['email'] ?? null;
        $senha = $body['senha'] ?? null;

        if (!$email || !$senha) {
            $this->json(['error' => 'email e senha sao obrigatorios'], 422);
        }

        $usuario = $this->usuario->getUserByEmail($email);

        if (!$usuario || !password_verify($senha, $usuario['senha'])) {
            $this->json(['error' => 'Credenciais invalidas'], 401);
        }

        $payload = [
            'sub' => (int) $usuario['id'],
            'iat' => time(),
            'exp' => time() + (60 * 60 * 8)
        ];

        $token = JWT::encode($payload, $this->secret, 'HS256');

        $this->json([
            'message' => 'Login realizado com sucesso',
            'token'   => $token,
            'usuario' => [
                'id'    => (int) $usuario['id'],
                'nome'  => $usuario['nome'],
                'email' => $usuario['email']
            ]
        ]);
    }

    // POST /auth/logout
    public function logout(): void
    {
        $this->json(['message' => 'Logout realizado com sucesso']);
    }
}