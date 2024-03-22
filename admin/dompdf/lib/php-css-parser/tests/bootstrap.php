<?php

final class ClassAutoloader {
    private static $baseDir = __DIR__ . '/../lib/';

    public static function register() {
        spl_autoload_register([__CLASS__, 'loadClass']);
    }

    private static function loadClass(string $class) {
        $file = self::$baseDir . str_replace('\\', '/', $class) . '.php';

        if (file_exists($file)) {
            require $file;
            return true;
        }

        return false;
    }
}

ClassAutoloader::register();
