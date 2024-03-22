<?php

namespace Sabberworm\CSS\CSSList;

use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\CSSList\CSSBlockList;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\RuleSet;
use Sabberworm\CSS\Value;

/**
 * The root CSSList of a parsed file. Contains all top-level css contents, mostly declaration blocks, but also any @-rules encountered.
 */
class Document extends CSSBlockList
{
    /**
     * Document constructor.
     * @param int $iLineNo
     */
    public function __construct(int $iLineNo = 0)
    {
        parent::__construct($iLineNo);
    }

    /**
     * Document factory method.
     * @param ParserState $oParserState
     * @return static
     */
    public static function parse(ParserState $oParserState): self
    {
        $oDocument = new static($oParserState->currentLine());
        CSSList::parseList($oParserState, $oDocument);
        return $oDocument;

