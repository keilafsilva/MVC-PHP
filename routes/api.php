<?php

/** @var App\Core\Router $router */

$router->get('/', 'HomeController', 'index');
$router->get('/home', 'HomeController', 'index');
$router->get('/home/login', 'HomeController', 'index');
$router->get('/home/tasks', 'HomeController', 'tasks');
$router->get('/home/users', 'HomeController', 'users');

// Auth
$router->post('/auth/login',  'AuthController',  'login');
$router->post('/auth/logout', 'AuthController',  'logout');

// Users
$router->post(  '/users',      'UserController', 'store');
$router->get(   '/users/{id}', 'UserController', 'show');
$router->get('/users', 'UserController', 'index');
$router->put(   '/users/{id}', 'UserController', 'update');
$router->delete('/users/{id}', 'UserController', 'destroy');

// Tasks
$router->post(  '/tasks',      'TaskController', 'store');
$router->get(   '/tasks',      'TaskController', 'index');
$router->get(   '/tasks/{id}', 'TaskController', 'show');
$router->put(   '/tasks/{id}', 'TaskController', 'update');
$router->delete('/tasks/{id}', 'TaskController', 'destroy');