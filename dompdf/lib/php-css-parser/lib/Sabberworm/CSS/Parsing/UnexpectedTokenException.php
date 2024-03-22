<?php

namespace Sabberworm\CSS\Parsing;

/**
 * Thrown if the CSS parsers encounters a token it did not expect
 */
class UnexpectedTokenException extends SourceException
{
    private $sExpected;
    private $sFound;
    private $sMatchType;

    public function __construct(string $sExpected, string $sFound, string $sMatchType = 'literal', int $iLineNo = 0)
    {
        $this->sExpected = $sExpected;
        $this->sFound = $sFound;
        $this->sMatchType = $sMatchType;

        parent::__construct(
            $this->getMessage($sExpected, $sFound, $sMatchType),
            $iLineNo
        );
    }

    private function getMessage(string $sExpected, string $sFound, string $sMatchType): string
    {
        $message = "Token “{$sExpected}” ({$sMatchType}) not found. Got “{$sFound}”.";

        switch ($sMatchType) {
            case 'search':
                $message = "Search for “{$sExpected}” returned no results. Context: “{$sFound}”.";
                break;
            case 'count':
                $message = "Next token was expected to have {$sExpected} chars. Context: “{$sFound}”.";
                break;
            case 'identifier':
                $message = "Identifier expected. Got “{$sFound}”";
                break;
            case 'custom':
                $message = trim("$sExpected $sFound");
                break;
        }

        return $message;
    }
}
