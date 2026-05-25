<?php

function dd(...$vars) : never
{
    echo '<pre style="background: #fff; 
    padding: 10px; 
    border: 1px solid #ccc;">';
    echo '<strong>Debug Output:</strong><br>';
    foreach ($vars as $var) {
        echo '<pre style="background: #f0f0f0; 
    padding: 10px; 
    border: 1px solid #ccc;">';
        var_dump($var);
        echo '</pre>';
    }
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
    echo '<strong>Called from:</strong> ' . $backtrace['file'] . ' on line ' . $backtrace['line'];      
    
    echo '</pre>';
    die();
}

function config(string $key, mixed $default = null) : mixed
{
    $config = require __DIR__ . '/../config/config.php';
    return $config[$key] ?? $default;
}
