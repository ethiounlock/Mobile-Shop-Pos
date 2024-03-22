<?php

namespace Sabberworm\CSS\Value;

use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;
use Sabberworm\CSS\Exception;

class LineName extends ValueList
{
    /**
     * @param array<string> $aComponents
     * @param int $iLineNo
     */
    public function __construct(array $aComponents = [], int $iLineNo = 0)
    {
        parent::__construct($aComponents, ' ', $iLineNo);
    }

    /**
     * @param ParserState $oParserState
     * @return static
     */
    public static function parse(ParserState $oParserState): self
    {
        $oParserState->consume('[');
        $oParserState->consumeWhiteSpace();
        $aNames = [];
        do {
            if ($oParserState->getSettings()->bLenientParsing) {
                try {
                    $aNames[] = $oParserState->parseIdentifier();
                } catch (UnexpectedTokenException $e) {
                    if (!$oParserState->comes(']')) {
                        throw new Exception('Expected identifier or closing bracket, got: ' . $oParserState->getCurrentToken());
                    }
                    break;
                }
            } else {
                $aNames[] = $oParserState->parseIdentifier();
            }
            $oParserState->consumeWhiteSpace();
        } while (!$oParserState->comes(']'));
        $oParserState->consume(']');

        if (
