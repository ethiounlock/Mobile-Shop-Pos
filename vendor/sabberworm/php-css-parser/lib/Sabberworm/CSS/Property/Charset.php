<?php

namespace Sabberworm\CSS\Property;

use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\CSSList;
use Sabberworm\CSS\Comment;

/**
 * Class representing an @charset rule.
 * The following restrictions apply:
 * • May not be found in any CSSList other than the Document.
 * • May only appear at the very top of a Document’s contents.
 * • Must not appear more than once.
 */
class Charset implements AtRule
{
    /**
     * @var string
     */
    private $sCharset;

    /**
     * @var int
     */
    protected $iLineNo;

    /**
     * @var Comment[]
     */
    protected $aComments;

    /**
     * Charset constructor.
     * @param string $sCharset
     * @param int $iLineNo
     */
    public function __construct(string $sCharset, int $iLineNo = 0)
    {
        $this->sCharset = $sCharset;
        $this->iLineNo = $iLineNo;
        $this->aComments = [];
    }

    /**
     * @return int
     */
    public function getLineNo(): int
    {
        return $this->iLineNo;
    }

    /**
     * @param string $sCharset
     */
    public function setCharset(string $sCharset): void
    {
        $this->sCharset = $sCharset;
    }

    /**
     * @return string
     */
    public function getCharset(): string
    {
        return $this->sCharset;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render(new OutputFormat());
    }

    /**
     * @param OutputFormat $oOutputFormat
     * @return string
     */
    public function render(OutputFormat $oOutputFormat): string
    {
        return "@charset {$this->sCharset};";
    }

    /**
     * @return string
     */
    public function atRuleName
