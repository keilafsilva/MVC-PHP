<?php
require_once __DIR__ . '/../app/core/Router.php';

use App\Core\Router;

$url = $_GET['url'] ?? '';
$router = new Router();
$router->dispatch($url);