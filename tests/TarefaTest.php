<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Models\Tarefa;
use App\Core\Database;

class TarefaTest extends TestCase
{
    private MockObject $dbMock;
    private Tarefa $tarefa;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(Database::class);
        $this->tarefa = new Tarefa($this->dbMock);
    }

    // --- createTarefa ---

    public function testCreateTarefaRetornaIdQuandoSucesso(): void
    {
        $this->dbMock->method('execute')->willReturn(true);
        $this->dbMock->method('lastInsertId')->willReturn('3');

        $id = $this->tarefa->createTarefa('Titulo teste', 'Descricao', 1, 2);

        $this->assertEquals(3, $id);
    }

    public function testCreateTarefaSemAtribuicaoRetornaId(): void
    {
        $this->dbMock->method('execute')->willReturn(true);
        $this->dbMock->method('lastInsertId')->willReturn('4');

        $id = $this->tarefa->createTarefa('Titulo teste', null, 1, null);

        $this->assertEquals(4, $id);
    }

    public function testCreateTarefaRetornaFalseQuandoFalha(): void
    {
        $this->dbMock->method('execute')->willReturn(true);
        $this->dbMock->method('lastInsertId')->willReturn('0');

        $id = $this->tarefa->createTarefa('Titulo', 'Desc', 1, null);

        $this->assertFalse($id);
    }

    // --- getAllTarefas ---

    public function testGetAllTarefasRetornaArray(): void
    {
        $esperado = [
            ['id' => 1, 'titulo' => 'Tarefa 1', 'status' => 'pendente', 'criado_por_id' => 1],
            ['id' => 2, 'titulo' => 'Tarefa 2', 'status' => 'concluida', 'criado_por_id' => 2],
        ];

        $this->dbMock->method('fetchAll')->willReturn($esperado);

        $result = $this->tarefa->getAllTarefas();

        $this->assertCount(2, $result);
        $this->assertEquals('Tarefa 1', $result[0]['titulo']);
    }

    public function testGetAllTarefasRetornaVazioSemTarefas(): void
    {
        $this->dbMock->method('fetchAll')->willReturn([]);

        $result = $this->tarefa->getAllTarefas();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    // --- getTarefaById ---

    public function testGetTarefaByIdRetornaTarefaExistente(): void
    {
        $esperado = ['id' => 1, 'titulo' => 'Tarefa 1', 'status' => 'pendente', 'criado_por_id' => 1];

        $this->dbMock->method('fetch')->willReturn($esperado);

        $result = $this->tarefa->getTarefaById(1);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Tarefa 1', $result['titulo']);
    }

    public function testGetTarefaByIdRetornaFalseQuandoNaoEncontrada(): void
    {
        $this->dbMock->method('fetch')->willReturn(false);

        $result = $this->tarefa->getTarefaById(999);

        $this->assertFalse($result);
    }

    // --- getTarefasByUsuario ---

    public function testGetTarefasByUsuarioRetornaListaFiltrada(): void
    {
        $esperado = [
            ['id' => 1, 'titulo' => 'Tarefa atribuida', 'atribuido_a_id' => 2],
        ];

        $this->dbMock->method('fetchAll')->willReturn($esperado);

        $result = $this->tarefa->getTarefasByUsuario(2);

        $this->assertCount(1, $result);
        $this->assertEquals(2, $result[0]['atribuido_a_id']);
    }

    // --- updateTarefa ---

    public function testUpdateTarefaRetornaTrueQuandoAtualiza(): void
    {
        $this->dbMock->method('execute')->willReturn(true);

        $result = $this->tarefa->updateTarefa(1, ['status' => 'concluida']);

        $this->assertTrue($result);
    }

    public function testUpdateTarefaRetornaFalseComDadosVazios(): void
    {
        $result = $this->tarefa->updateTarefa(1, []);

        $this->assertFalse($result);
    }

    public function testUpdateTarefaAtualizaMultiplosCampos(): void
    {
        $this->dbMock->method('execute')->willReturn(true);

        $result = $this->tarefa->updateTarefa(1, [
            'titulo'    => 'Novo titulo',
            'descricao' => 'Nova descricao',
            'status'    => 'em_andamento',
        ]);

        $this->assertTrue($result);
    }

    // --- deleteTarefa ---

    public function testDeleteTarefaRetornaTrueQuandoSucesso(): void
    {
        $this->dbMock->method('execute')->willReturn(true);

        $result = $this->tarefa->deleteTarefa(1);

        $this->assertTrue($result);
    }
}
