<?php

namespace App\Core;

use App\Controllers\Error\HttpErrorController;

class Router
{
    public function dispatch($url)
    {
        $url = trim($url, '/');
        $parts = $url ? explode('/', $url) : [];
        $controllerClass = 'App\\Controllers\\' . ucfirst($parts[0] ?? 'Home') . 'Controller';

        $actionName = $parts[1] ?? 'index';

        if (!class_exists($controllerClass)) {
            $httpErrorController = new HttpErrorController();
            $httpErrorController->notFound();
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $actionName)) {
            $httpErrorController = new HttpErrorController();
            $httpErrorController->notFound();
            return;
        }

        $params = array_slice($parts, 2);
        call_user_func_array([$controller, $actionName], $params);
    }
}
