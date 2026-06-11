<?php

namespace App\Controllers;

use App\Core\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function index(?Request $request = null)
    {
        $this->view('home/index');
        return;
    }

    public function tasks(): void
    {
        $this->view('tasks/index');
    }

    public function users(): void
    {
        $this->view('users/index');
    }


}
