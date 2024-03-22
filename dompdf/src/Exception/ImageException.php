<?php

/**
 * This file contains the ImageException class for the DOMPDF package.
 *
 * @package dompdf
 */

namespace Dompdf\Exception;

use Dompdf\Exception;

/**
 * Image exception thrown by DOMPDF
 *
 * @package dompdf
 */
class ImageException extends Exception
{

    /**
     * Class constructor
     *
     * @param string $message Error message
     * @param int $code       Error code
     */
    public function __construct(string $message = null, int $code = 0)
    {
        parent::__construct($message, $code);
    }

}
