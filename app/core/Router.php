<?php

namespace App\Core;

use App\Controllers\Error\HttpErrorController;
use Symfony\Component\HttpFoundation\Request;

class Router
{
    public function dispatch(Request $request) : void
    {
        $string_url = $request->query->get('url', '');
        $url = trim($string_url, '/');
        $parts = $url ? explode('/', $url) : [];
        $controllerClass = $parts[0] ?? 'home';
        $controllerClass = 'App\\Controllers\\' . ucfirst($controllerClass) . 'Controller';
        $actionName = $parts[1] ?? 'index';
        //dd($controllerClass, $actionName, $parts, $url);

        if (!class_exists($controllerClass)) {
            $httpErrorController = new HttpErrorController();
            $httpErrorController->notFound();
            return;
        }

        $controller = new $controllerClass();
        $this->wireRequest($controller, $request);

        if (!method_exists($controller, $actionName)) {
            $httpErrorController = new HttpErrorController();
            $httpErrorController->notFound();
            return;
        }

        $params = array_slice($parts, 2);
        call_user_func_array([$controller, $actionName], $params);
    }

    private function wireRequest(Object $controller, Request $request) : void
    {
        if ($controller instanceof Controller) {
            $controller->setRequest($request);
        }
    }
}
