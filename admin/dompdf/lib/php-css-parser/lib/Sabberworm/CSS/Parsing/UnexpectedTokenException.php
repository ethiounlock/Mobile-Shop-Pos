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

    private const MATCH_TYPES = [
        'literal',
        'identifier',
        'count',
        'expression',
        'search',
        'custom'
    ];

    public function __construct(
        string $sExpected,
        string $sFound,
        string $sMatchType = 'literal',
        int $iLineNo = 0
    ) {
        $this->sExpected = $sExpected;
        $this->sFound = $sFound;
        $this->sMatchType = $sMatchType;

        parent::__construct(
            $this->getErrorMessage(),
            $iLineNo
        );
    }

    private function getErrorMessage(): string
    {
        if (!in_array($this->sMatchType, self::MATCH_TYPES, true)) {
            throw new \InvalidArgumentException(
                'Invalid match type: ' . $this->sMatchType
            );
        }

        $message = match ($this->sMatchType) {
            'literal' => "Token “{$this->sExpected}” not found. Got “{$this->sFound}”.",
            'identifier' => "Identifier expected. Got “{$this->
