<?php

/**
 * @package dompdf
 * @link    http://dompdf.github.com/
 * @author  Benj Carson <benjcarson@digitaljunkies.ca>
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

// Sabberworm autoloader
function sabberwormAutoloader($class)
{
    if (strpos($class, 'Sabberworm') !== false) {
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        $file = realpath(__DIR__ . '/lib/php-css-parser/lib/' . (empty($file) ? '' : DIRECTORY_SEPARATOR) . $file . '.php');

        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }

    return false;
}

spl_autoload_register('sabberwormAutoloader');

// Include php-font-lib
require_once __DIR__ . '/lib/php-font-lib/src/FontLib/Autoloader.php';

// Include php-svg-lib
require_once __DIR__ . '/lib/php-svg-lib/src/autoload.php';

// Include dompdf's Autoloader
require_once __DIR__ . '/src/Autoloader.php';
Dompdf\Autoloader::register();

