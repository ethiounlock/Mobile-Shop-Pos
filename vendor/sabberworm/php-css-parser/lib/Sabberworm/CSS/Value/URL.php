<?php

namespace Sabberworm\CSS\Value;

use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\CSSString;

/**
 * URL class representing a CSS url() value.
 */
class URL extends PrimitiveValue
{
    private $url;

    /**
     * URL constructor.
     *
     * @param CSSString $url URL as a CSSString object
     * @param int $lineNo Line number for error reporting
     */
    public function __construct(CSSString $url, $lineNo = 0)
    {
        parent::__construct($lineNo);
        $this->setURL($url);
    }

    /**
     * Parse a url() value from a ParserState object.
     *
     * @param ParserState $oParserState ParserState object
     * @return URL URL object
     */
    public static function parse(ParserState $oParserState)
    {
        $bUseUrl = $oParserState->comes('url', true);
        if ($bUseUrl) {
            $oParserState->consume('url');
            $oParseState->consumeWhiteSpace();
            $oParserState->consume('(');
        }

        $oParserState->consumeWhiteSpace();

        // Validate the input as a CSSString
        $oResult = new URL(CSSString::parse($oParserState), $oParserState->currentLine());

        if ($bUseUrl) {
            $oParserState->consumeWhiteSpace();
            $oParserState->consume(')');
        }

        return $oResult;
    }

    /**
     * Set the URL.
     *
     * @param CSSString $url URL as a CSSString object
     */
    public function setURL(CSSString $url)
    {
        $this->url = $url;
    }

    /**
     * Get the URL.
     *
     * @return CSSString URL as a CSS
