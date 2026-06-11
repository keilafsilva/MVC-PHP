<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Models\Usuario;
use App\Core\Database;

class UsuarioTest extends TestCase
{
    private MockObject $dbMock;
    private Usuario $usuario;

    protected function setUp(): void
    {
        // Mock do Database para não precisar de conexão real
        $this->dbMock = $this->createMock(Database::class);

        $this->usuario = new Usuario($this->dbMock);
    }

    // --- createUser ---

    public function testCreateUserRetornaIdQuandoSucesso(): void
    {
        $this->dbMock->method('execute')->willReturn(true);
        $this->dbMock->method('lastInsertId')->willReturn('5');

        $id = $this->usuario->createUser('Jose Silva', 'jose@email.com', 'senha123');

        $this->assertEquals(5, $id);
    }

    public function testCreateUserRetornaFalseQuandoFalha(): void
    {
        $this->dbMock->method('execute')->willReturn(true);
        $this->dbMock->method('lastInsertId')->willReturn('0');

        $id = $this->usuario->createUser('Jose Silva', 'jose@email.com', 'senha123');

        $this->assertFalse($id);
    }

    // --- getAllUsers ---

    public function testGetAllUsersRetornaArray(): void
    {
        $esperado = [
            ['id' => 1, 'nome' => 'Jose', 'email' => 'jose@email.com', 'created_at' => '2024-01-01'],
            ['id' => 2, 'nome' => 'Maria', 'email' => 'maria@email.com', 'created_at' => '2024-01-02'],
        ];

        $this->dbMock->method('fetchAll')->willReturn($esperado);

        $result = $this->usuario->getAllUsers();

        $this->assertCount(2, $result);
        $this->assertEquals('Jose', $result[0]['nome']);
    }

    public function testGetAllUsersRetornaArrayVazioSemUsuarios(): void
    {
        $this->dbMock->method('fetchAll')->willReturn([]);

        $result = $this->usuario->getAllUsers();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    // --- getUserById ---

    public function testGetUserByIdRetornaUsuarioExistente(): void
    {
        $esperado = ['id' => 1, 'nome' => 'Jose', 'email' => 'jose@email.com', 'created_at' => '2024-01-01'];

        $this->dbMock->method('fetch')->willReturn($esperado);

        $result = $this->usuario->getUserById(1);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Jose', $result['nome']);
    }

    public function testGetUserByIdRetornaFalseQuandoNaoEncontrado(): void
    {
        $this->dbMock->method('fetch')->willReturn(false);

        $result = $this->usuario->getUserById(999);

        $this->assertFalse($result);
    }

    // --- getUserByEmail ---

    public function testGetUserByEmailRetornaUsuario(): void
    {
        $esperado = ['id' => 1, 'nome' => 'Jose', 'email' => 'jose@email.com', 'senha' => 'hash'];

        $this->dbMock->method('fetch')->willReturn($esperado);

        $result = $this->usuario->getUserByEmail('jose@email.com');

        $this->assertEquals('jose@email.com', $result['email']);
    }

    public function testGetUserByEmailRetornaFalseQuandoNaoEncontrado(): void
    {
        $this->dbMock->method('fetch')->willReturn(false);

        $result = $this->usuario->getUserByEmail('naoexiste@email.com');

        $this->assertFalse($result);
    }

    // --- updateUser ---

    public function testUpdateUserRetornaTrueQuandoAtualiza(): void
    {
        $this->dbMock->method('execute')->willReturn(true);

        $result = $this->usuario->updateUser(1, ['nome' => 'Jose Atualizado']);

        $this->assertTrue($result);
    }

    public function testUpdateUserRetornaFalseComDadosVazios(): void
    {
        $result = $this->usuario->updateUser(1, []);

        $this->assertFalse($result);
    }

    public function testUpdateUserAtualizaSenhaComHash(): void
    {
        $this->dbMock->expects($this->once())
            ->method('execute')
            ->with(
                $this->stringContains('senha = :senha'),
                $this->callback(function ($params) {
                    return isset($params[':senha']) && password_verify('nova_senha', $params[':senha']);
                })
            )
            ->willReturn(true);

        $result = $this->usuario->updateUser(1, ['senha' => 'nova_senha']);

        $this->assertTrue($result);
    }

    // --- deleteUser ---

    public function testDeleteUserRetornaTrueQuandoSucesso(): void
    {
        $this->dbMock->method('execute')->willReturn(true);

        $result = $this->usuario->deleteUser(1);

        $this->assertTrue($result);
    }

    // --- unassignTasks ---

    public function testUnassignTasksRetornaTrueQuandoSucesso(): void
    {
        $this->dbMock->method('execute')->willReturn(true);

        $result = $this->usuario->unassignTasks(1);

        $this->assertTrue($result);
    }
}
