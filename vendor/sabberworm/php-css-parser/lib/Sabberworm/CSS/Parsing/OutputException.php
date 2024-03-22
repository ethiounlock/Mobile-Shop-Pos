<?php

namespace Sabberworm\CSS\Parsing;

/**
 * Thrown if the CSS parsers attempts to print something invalid
 */
class OutputException extends SourceException
{
    const ERROR_MESSAGE = 'An error occurred while printing CSS output: line %d';

    public function __construct($iLineNo = 0) {
        $sMessage = sprintf(self::ERROR_MESSAGE, $iLineNo);
        parent::__construct($sMessage, $iLineNo);
    }
}
