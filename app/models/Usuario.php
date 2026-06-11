<?php

namespace App\Models;

use App\Core\Database;

use App\Core\Model;

class Usuario extends Model
{
    public function __construct(?Database $db = null)
    {
        if ($db !== null) {
            $this->db = $db;
        } else {
            parent::__construct();
        }
    }
    public function createUser(string $nome, string $email, string $senha): int|false
    {
        $this->db->execute(
            "INSERT INTO usuarios (nome, email, senha, created_at) VALUES (:nome, :email, :senha, NOW())",
            [':nome' => $nome, ':email' => $email, ':senha' => password_hash($senha, PASSWORD_BCRYPT)]
        );
        return (int) $this->db->lastInsertId() ?: false;
    }

    public function getAllUsers(): array
    {
        return $this->db->fetchAll(
            "SELECT id, nome, email, created_at FROM usuarios WHERE deleted_at IS NULL"
        );
    }

    public function getUserById(int $id): array|false
    {
        return $this->db->fetch(
            "SELECT id, nome, email, created_at FROM usuarios WHERE id = :id AND deleted_at IS NULL",
            [':id' => $id]
        );
    }

    public function getUserByEmail(string $email): array|false
    {
        return $this->db->fetch(
            "SELECT id, nome, email, senha FROM usuarios WHERE email = :email AND deleted_at IS NULL",
            [':email' => $email]
        );
    }

    public function updateUser(int $id, array $dados): bool
    {
        $campos = [];
        $params = [':id' => $id];

        if (!empty($dados['nome'])) {
            $campos[] = 'nome = :nome';
            $params[':nome'] = $dados['nome'];
        }
        if (!empty($dados['email'])) {
            $campos[] = 'email = :email';
            $params[':email'] = $dados['email'];
        }
        if (!empty($dados['senha'])) {
            $campos[] = 'senha = :senha';
            $params[':senha'] = password_hash($dados['senha'], PASSWORD_BCRYPT);
        }

        if (empty($campos)) return false;

        return $this->db->execute(
            "UPDATE usuarios SET " . implode(', ', $campos) . " WHERE id = :id",
            $params
        );
    }

    public function deleteUser(int $id): bool
    {
        return $this->db->execute(
            "UPDATE usuarios SET deleted_at = NOW() WHERE id = :id",
            [':id' => $id]
        );
    }

    public function unassignTasks(int $userId): bool
    {
        return $this->db->execute(
            "UPDATE tarefas SET atribuido_a_id = NULL WHERE atribuido_a_id = :id",
            [':id' => $userId]
        );
    }
}
