<?php

namespace App\Core;

use App\Controllers\Error\HttpErrorController;
use Symfony\Component\HttpFoundation\Request;

class Router
{
    private array $routes = [];

    public function get(string $path, string $controller, string $action): void
    {
        $this->addRoute('GET', $path, $controller, $action);
    }

    public function post(string $path, string $controller, string $action): void
    {
        $this->addRoute('POST', $path, $controller, $action);
    }

    public function put(string $path, string $controller, string $action): void
    {
        $this->addRoute('PUT', $path, $controller, $action);
    }

    public function delete(string $path, string $controller, string $action): void
    {
        $this->addRoute('DELETE', $path, $controller, $action);
    }

    private function addRoute(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'method'     => $method,
            'path'       => $path,
            'controller' => $controller,
            'action'     => $action,
        ];
    }

    public function dispatch(Request $request): void
    {
        $method = $request->getMethod();

        // Lê o parâmetro url igual ao router antigo
        $string_url = $request->query->get('url', '');
        $uri = '/' . trim($string_url, '/');
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $params = $this->matchRoute($route['path'], $uri);
            if ($params === null) continue;

            $controllerClass = 'App\\Controllers\\' . $route['controller'];

            if (!class_exists($controllerClass)) {
                (new HttpErrorController())->notFound();
                return;
            }

            $controller = new $controllerClass();
            if ($controller instanceof Controller) {
                $controller->setRequest($request);
            }

            if (!method_exists($controller, $route['action'])) {
                (new HttpErrorController())->notFound();
                return;
            }

            call_user_func_array([$controller, $route['action']], $params);
            return;
        }

        (new HttpErrorController())->notFound();
    }

    private function matchRoute(string $routePath, string $uri): ?array
    {
        // Converte /users/{id} em regex
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // remove o match completo
            return $matches;       // retorna só os parâmetros capturados
        }

        return null;
    }
}
