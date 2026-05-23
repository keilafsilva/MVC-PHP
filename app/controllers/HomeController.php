<?php

require_once __DIR__ . '/../core/Controller.php';

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $this->view('home/index');
        return;
    }

    public function contact()
    {
        $this->view('home/contact');
        return;
    }
}
?>