<?php

namespace Sabberworm\CSS\CSSList;

use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\CSSList\CSSBlockList;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Rule\RuleSet;
use Sabberworm\CSS\Value\Value;

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
     * Parses a CSS document and returns a Document object.
     * @param ParserState $oParserState
     * @return Document
     */
    public static function parse(ParserState $oParserState): Document
    {
        $oDocument = new Document($oParserState->currentLine());
        CSSList::parseList($oParserState, $oDocument);
        return $oDocument;
    }

    /**
     * Gets all DeclarationBlock objects recursively.
     * @return DeclarationBlock[]
     */
    public function getAllDeclarationBlocks(): array
    {
        $aResult = [];
        $this->allDeclarationBlocks($aResult);
        return $aResult;
    }

    /**
     * Returns all RuleSet objects found recursively in the tree.
     * @return RuleSet[]
     */
    public function getAllRuleSets(): array
    {
        $aResult = [];
        $this->allRuleSets($aResult);
        return $aResult;

