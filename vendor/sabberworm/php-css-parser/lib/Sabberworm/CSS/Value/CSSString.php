<?php

namespace Sabberworm\CSS\Value;

use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\SourceException;
use Sabberworm\CSS\OutputFormat;

class CSSString extends PrimitiveValue
{
    /** @var string */
    private $string;

    /**
     * CSSString constructor.
     *
     * @param string $string
     * @param int    $lineNo
     */
    public function __construct(string $string, int $lineNo = 0)
    {
        $this->string = $string;
        parent::__construct($lineNo);
    }

    /**
     * @param ParserState $oParserState
     *
     * @return static
     * @throws SourceException
     */
    public static function parse(ParserState $oParserState): self
    {
        $sBegin = $oParserState->peek();
        $sQuote = null;

        if ($sBegin === "'") {
            $sQuote = "'";
        } elseif ($sBegin === '"') {
            $sQuote = '"';
        }

        if ($sQuote !== null) {
            $oParserState->consume($sQuote);
        }

        $sResult = '';
        $sContent = null;

        if ($sQuote === null) {
            // Unquoted strings end in whitespace or with braces, brackets, parentheses
            while (!preg_match('/[\\s{}()<>\\[\\]]/isu', $oParserState->peek())) {
                $sResult .= $oParserState->parseCharacter(false);
            }
        } else {
            while (!$oParserState->comes($sQuote)) {
                $sContent = $oParserState->parseCharacter(false);

                if ($sContent === null) {
                    throw new SourceException("Non-well-formed quoted string {$oParserState->peek(3)}", $oParserState->currentLine());
                }

                $sResult .= $sContent;
            }

            $oParserState->consume($sQuote);
        }

        return new self($sResult, $oParserState->currentLine());
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->string);
    }

    /**
     * @return string
     */
    public function getString(): string
    {
        return $this->string;
    }

    /**
     * @param string $string
     */
    public function setString(string $string
