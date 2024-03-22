<?php

namespace Sabberworm\CSS;

use Sabberworm\CSS\CSSList\Document;
use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Settings;

/**
 * Parser class parses CSS from text into a data structure.
 */
class Parser
{
    private $oParserState;

    /**
     * Parser constructor.
     * Note that that iLineNo starts from 1 and not 0
     *
     * @param string $sText
     * @param Settings|null $oParserSettings
     * @param int $iLineNo
     */
    public function __construct(string $sText, Settings $oParserSettings = null, int $iLineNo = 1)
    {
        if ($oParserSettings === null) {
            $oParserSettings = Settings::create();
        }
        $this->oParserState = new ParserState($sText, $oParserSettings, $iLineNo);
    }

    /**
     * Sets the charset for the parser state.
     *
     * @param string $sCharset
     */
    public function setCharset(string $sCharset): void
    {
        $this->oParserState->setCharset($sCharset);
    }

    /**
     * Gets the charset for the parser state.
     *
     * @return string
     */
    public function getCharset(): string
    {
        return $this->oParserState->getCharset();

