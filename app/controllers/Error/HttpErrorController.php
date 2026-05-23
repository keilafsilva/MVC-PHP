<?php

require_once __DIR__ . '../../../core/Controller.php';
use App\Core\Controller;

class HttpErrorController extends Controller
{
    public function notFound()
    {
        http_response_code(404);
        $this->view('errors/404');
        return;
    }

    public function internalServerError()
    {
        http_response_code(500);
        $this->view('errors/500');
        return;
    }

    public function forbidden()
    {
        http_response_code(403);
        $this->view('errors/403');
        return;
    }
}
