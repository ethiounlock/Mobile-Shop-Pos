<?php

namespace Sabberworm\CSS\Property;

use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\CSSList\CSSList;

/**
 * CSSNamespace represents an @namespace rule.
 */
class CSSNamespace implements AtRule
{
    private $mUrl;
    private $sPrefix;
    private $iLineNo;
    protected $aComments;

    /**
     * CSSNamespace constructor.
     *
     * @param CSSList|string          $mUrl
     * @param string|null             $sPrefix
     * @param int                     $iLineNo
     */
    public function __construct($mUrl, $sPrefix = null, $iLineNo = 0)
    {
        $this->mUrl = is_a($mUrl, CSSList::class) ? $mUrl : new CSSList([$mUrl]);
        $this->sPrefix = $sPrefix;
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

    public function __toString()
    {
        return $this->render(new OutputFormat());
    }

    public function render(OutputFormat $oOutputFormat): string
    {
        return '@namespace ' . ($this->sPrefix ?? '') . $this->mUrl->render($oOutputFormat) . ';';
    }

    public function getUrl()
    {
        return $this->mUrl;
    }

    public function getPrefix()
    {
        return $this->sPrefix;
    }

    public function setUrl($mUrl)
    {
        $this->mUrl = $mUrl;
    }

    public function setPrefix($sPrefix)
    {
        $this->sPrefix = $sPrefix;
    }


