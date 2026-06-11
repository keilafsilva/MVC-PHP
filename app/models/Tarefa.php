<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database; 

class Tarefa extends Model
{

public function __construct(?Database $db = null)
    {
        if ($db !== null) {
            $this->db = $db;
        } else {
            parent::__construct();
        }
    }
    public function createTarefa(string $titulo, ?string $descricao, int $criadoPorId, ?int $atribuidoAId): int|false
    {
        $atribuido = ($atribuidoAId !== null && $atribuidoAId > 0) ? $atribuidoAId : null;

        $this->db->execute(
            "INSERT INTO tarefas (titulo, descricao, criado_por_id, atribuido_a_id, created_at) 
             VALUES (:titulo, :descricao, :criado_por_id, :atribuido_a_id, NOW())",
            [
                ':titulo'         => $titulo,
                ':descricao'      => $descricao,
                ':criado_por_id'  => $criadoPorId,
                ':atribuido_a_id' => $atribuido
            ]
        );
        return (int) $this->db->lastInsertId() ?: false;
    }

    public function getAllTarefas(): array
    {
        return $this->db->fetchAll(
            "SELECT t.id, t.titulo, t.descricao, t.status, t.criado_por_id, t.atribuido_a_id,
                    t.created_at, t.updated_at,
                    uc.nome AS criador_nome,
                    ut.nome AS atribuido_nome
             FROM tarefas t
             INNER JOIN usuarios uc ON t.criado_por_id = uc.id
             LEFT JOIN  usuarios ut ON t.atribuido_a_id = ut.id
             WHERE t.deleted_at IS NULL"
        );
    }

    public function getTarefaById(int $id): array|false
    {
        return $this->db->fetch(
            "SELECT t.id, t.titulo, t.descricao, t.status, t.criado_por_id, t.atribuido_a_id,
                    t.created_at, t.updated_at,
                    uc.nome AS criador_nome,
                    ut.nome AS atribuido_nome
             FROM tarefas t
             INNER JOIN usuarios uc ON t.criado_por_id = uc.id
             LEFT JOIN  usuarios ut ON t.atribuido_a_id = ut.id
             WHERE t.id = :id AND t.deleted_at IS NULL",
            [':id' => $id]
        );
    }

    public function getTarefasByUsuario(int $usuarioId): array
    {
        return $this->db->fetchAll(
            "SELECT t.id, t.titulo, t.descricao, t.status, t.criado_por_id, t.atribuido_a_id,
                    t.created_at, t.updated_at,
                    uc.nome AS criador_nome,
                    ut.nome AS atribuido_nome
             FROM tarefas t
             INNER JOIN usuarios uc ON t.criado_por_id = uc.id
             LEFT JOIN  usuarios ut ON t.atribuido_a_id = ut.id
             WHERE t.atribuido_a_id = :usuario_id AND t.deleted_at IS NULL",
            [':usuario_id' => $usuarioId]
        );
    }

    public function getTarefasRelacionadasAoUsuario(int $usuarioId): array
    {
        return $this->db->fetchAll(
            "SELECT t.id, t.titulo, t.descricao, t.status, t.criado_por_id, t.atribuido_a_id,
                    t.created_at, t.updated_at,
                    uc.nome AS criador_nome,
                    ut.nome AS atribuido_nome
             FROM tarefas t
             INNER JOIN usuarios uc ON t.criado_por_id = uc.id
             LEFT JOIN  usuarios ut ON t.atribuido_a_id = ut.id
             WHERE (t.criado_por_id = :usuario_id OR t.atribuido_a_id = :usuario_id)
               AND t.deleted_at IS NULL",
            [':usuario_id' => $usuarioId]
        );
    }

    public function updateTarefa(int $id, array $dados): bool
    {
        $campos = [];
        $params = [':id' => $id];

        if (!empty($dados['titulo'])) {
            $campos[] = 'titulo = :titulo';
            $params[':titulo'] = $dados['titulo'];
        }
        if (isset($dados['descricao'])) {
            $campos[] = 'descricao = :descricao';
            $params[':descricao'] = $dados['descricao'];
        }
        if (!empty($dados['status'])) {
            $statusValidos = ['pendente', 'em_andamento', 'concluida'];
            if (!in_array($dados['status'], $statusValidos)) {
                return false;
            }
            $campos[] = 'status = :status';
            $params[':status'] = $dados['status'];
        }
        if (isset($dados['atribuido_a_id'])) {
            $campos[] = 'atribuido_a_id = :atribuido_a_id';
            $params[':atribuido_a_id'] = ($dados['atribuido_a_id'] !== null && $dados['atribuido_a_id'] > 0)
                ? (int) $dados['atribuido_a_id']
                : null;
        }

        if (empty($campos)) return false;

        $campos[] = 'updated_at = NOW()';

        return $this->db->execute(
            "UPDATE tarefas SET " . implode(', ', $campos) . " WHERE id = :id",
            $params
        );
    }

    public function deleteTarefa(int $id): bool
    {
        return $this->db->execute(
            "UPDATE tarefas SET deleted_at = NOW() WHERE id = :id",
            [':id' => $id]
        );
    }
}
