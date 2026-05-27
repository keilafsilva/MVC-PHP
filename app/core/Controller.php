<?php
namespace App\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Controller
{
    protected Request $request;

    public function setRequest(Request $request) : void
    {
        $this->request = $request;
    }

    protected function view(string $view, array $viewData = []) : void
    {
        extract($viewData);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: " . $viewFile);
        }
        require_once $viewFile;
    }

    protected function json(array $data, int $statusCode = 200) : never
    {
        $response = new JsonResponse($data, $statusCode);
        $response->send();
        exit;
    }

    protected function redirect(string $url, int $statusCode = 302) : never
    {
        $response = new redirectResponse($url, $statusCode);
        $response->send();
        exit;
    }
}
?>