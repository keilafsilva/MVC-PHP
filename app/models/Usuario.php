<?php
namespace App\Models;

use App\Core\Model;

class Usuario extends Model
{
    public function getUserData()
    {
        return [
            'id' => 1,
            'nome' => 'Juan Pérez',
            'email' => 'juan.perez@example.com'
        ];
    }

    public function createUser($name)
    {
        // Lógica para criar um novo usuário no banco de dados
        $sql = "INSERT INTO usuarios (nome) VALUES (:nome)";
        $params = [
            ':nome' => $name
        ];

        return $this->db->execute($sql, $params);
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM usuarios";
        return $this->db->fetchAll($sql);
    }

    public function getUserById($id)
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $params = [
            ':id' => $id
        ];
        return $this->db->fetch($sql, $params);
    }

    public function getUserCount()
    {
        $sql = "SELECT COUNT(*) as count FROM usuarios";
        $result = $this->db->fetch($sql);
        return $result['count'];
    }

}