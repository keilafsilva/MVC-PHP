<?php

namespace App\Core;

require_once __DIR__ . '/../controllers/homeController.php';
require_once __DIR__ . '/../controllers/NoticiasController.php';
require_once __DIR__ . '/../controllers/Error/HttpErrorController.php';

class Router
{
    public function dispatch($url)
    {
        $url = trim($url, '/');
        $parts = $url ? explode('/', $url) : [];

        $controllerName = $parts[0] ?? 'Home';
        $controllerName = ucfirst($controllerName) . 'Controller';

        // classes de controllers estão no namespace global (sem namespace),
        // então usamos o nome totalmente qualificado com barra inicial
        $controllerClass = '\\' . $controllerName;

        $httpErrorControllerClass = '\\HttpErrorController';
        

        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            $actionName = $parts[1] ?? 'index';
            $params = array_slice($parts, 2);

            if (method_exists($controller, $actionName)) {
                call_user_func_array([$controller, $actionName], $params);
            } else {
                $HttpErrorController = new $httpErrorControllerClass();
                $HttpErrorController->notFound();
            }
        } else {
            if (class_exists($httpErrorControllerClass)) {
                $HttpErrorController = new $httpErrorControllerClass();
                $HttpErrorController->notFound();
            } else {
                echo "404 Not Found";
            }
            return;
        }
    }
}
?>