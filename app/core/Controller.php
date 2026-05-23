<?php
namespace App\Core;

class Controller
{
    protected function view($view)
    {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        require_once $viewFile;
    }
}
?>