<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

class HomeController extends Controller
{
    public function index()
    {
        $usuario = new Usuario();
        $data = $usuario->getUserData();
        echo 'Criando novo usuário:';
        $usuarioCriado = $usuario->createUser('Ana Gomes');
        echo 'Usuário criado com sucesso, retorno: ' . $usuarioCriado . '<br>';

        echo 'Listando todos os usuários: <br>';
        $usuarios = $usuario->getAllUsers();
        foreach ($usuarios as $user) {
            echo 'ID: ' . $user['id'] . ' - Nome: ' . $user['nome'] . '<br>';
        }

        $usuarioPorID = $usuario->getUserById(6);
        echo 'Usuário com ID 6: ' . $usuarioPorID['nome'] . '<br>';

        echo 'Total de usuários: ' . $usuario->getUserCount() . '<br>';


        dd(config('database'));
        $this->view('home/index');
        return;
    }



    public function contact()
    {
        $this->view('home/contact');
        return;
    }
}
