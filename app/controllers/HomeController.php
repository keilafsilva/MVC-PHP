<?php
namespace App\Controllers;

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