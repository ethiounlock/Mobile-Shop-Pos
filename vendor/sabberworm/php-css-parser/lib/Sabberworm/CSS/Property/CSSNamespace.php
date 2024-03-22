<?php

namespace Sabberworm\CSS\Property;

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
     * @param string|null $sPrefix
     * @param int $iLineNo
     */
    public function __construct($mUrl, $sPrefix = null, $iLineNo = 0)
    {
        $this->mUrl = $mUrl;
        $this->sPrefix = $sPrefix;
        $this->iLineNo = $iLineNo;
        $this->aComments = [];
    }

    /**
     * @return int
     */
    public function getLineNo()
    {
        return $this->iLineNo;
    }

    public function __toString()
    {
        return $this->render(new \Sabberworm\CSS\OutputFormat());
    }

    public function render(\Sabberworm\CSS\OutputFormat $oOutputFormat)
    {
        return '@namespace ' . ($this->sPrefix === null ? '' : $this->sPrefix . ' ') . $this->mUrl->render($oOutputFormat) . ';';
    }

    public function getUrl()
    {
        return $this->mUrl;
    }

    public function getPrefix()
    {
        return $this->sPrefix;
    }

    /**
     * @param string $mUrl
     * @return static
     */
    public function withUrl($mUrl)
    {
        $new = clone $this;
        $new->mUrl = $mUrl;
        return $new;
    }

   
