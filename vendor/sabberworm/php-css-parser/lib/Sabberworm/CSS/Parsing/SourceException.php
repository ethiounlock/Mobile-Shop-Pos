<?php

namespace Sabberworm\CSS\Parsing;

class SourceException extends \Exception
{
    private $lineNumber;

    public function __construct(string $message, int $lineNumber = 0)
    {
        $this->lineNumber = $lineNumber;
        $formattedMessage = $this->formatMessage($message, $lineNumber);
        parent::__construct($formattedMessage);
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    private function formatMessage(string $message, int $lineNumber): string
    {
        if ($lineNumber > 0) {
            $message .= " [line no: $lineNumber]";
        }

        return $message;
    }
}
