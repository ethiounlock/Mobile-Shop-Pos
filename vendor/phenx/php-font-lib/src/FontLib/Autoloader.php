<?php

/**
 * Autoloader for FontLib
 *
 * @package php-font-lib
 */
namespace FontLib;

/**
 * Class Autoloader
 */
class Autoloader
{
    const PREFIX = 'FontLib';

    /**
     * Register the autoloader
     */
    public static function register()
    {
        spl_autoload_register([new self, 'autoload']);
    }

    /**
     * Autoloader
     *
     * @param string $class
     */
    public static function autoload($class)
    {
        $prefixLength = strlen(self::PREFIX);
        if (strncmp(self::PREFIX, $class, $prefixLength) === 0) {
            $file = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, $prefixLength)) . '.php';
            $file = realpath(__DIR__ . DIRECTORY_SEPARATOR . $file);
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
}

Autoloader::register();
