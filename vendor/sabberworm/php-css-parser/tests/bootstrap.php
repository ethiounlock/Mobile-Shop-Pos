<?php

function autoloadClass($class)
{
    $file = __DIR__ . '/../lib/' . str_replace('\\', '/', $class) . '.php';

    // Check if the file exists before requiring it
    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('autoloadClass');
