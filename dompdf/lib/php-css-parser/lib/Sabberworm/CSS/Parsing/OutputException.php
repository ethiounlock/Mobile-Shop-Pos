<?php

namespace Sabberworm\CSS\Parsing;

/**
 * Thrown if the CSS parsers attempts to print something invalid
 */
class OutputException extends SourceException
{
    const INVALID_OUTPUT_MESSAGE = 'Attempted to output invalid value';

    public function __construct($iLineNo = 0) {
        parent::__construct(self::INVALID_OUTPUT_MESSAGE, $iLineNo);
    }
}
