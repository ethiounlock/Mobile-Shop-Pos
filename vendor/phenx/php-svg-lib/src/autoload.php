<?php

/**
 * Autoloader for the php-svg-lib library
 *
 * @package php-svg-lib
 * @link    http://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */

spl_autoload_register(function ($class) {
    if (strpos($class, 'Svg') === 0) {
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        $file = realpath(__DIR__ . DIRECTORY_SEPARATOR . $file);

        if (file_exists($file)) {
            include $file;
        }
    }
});
