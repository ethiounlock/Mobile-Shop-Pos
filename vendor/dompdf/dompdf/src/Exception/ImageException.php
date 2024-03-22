<?php

namespace Dompdf\Exception;

/**
 * @package dompdf
 * @link    http://dompdf.github.com/
 * @author  Benj Carson <benjcarson@digitaljunkies.ca>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

/**
 * Class ImageException
 * @package Dompdf\Exception
 */
class ImageException extends Exception
{
    /**
     * ImageException constructor.
     * @param string $message
     * @param int $code
     */
    public final function __construct(string $message = null, int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
