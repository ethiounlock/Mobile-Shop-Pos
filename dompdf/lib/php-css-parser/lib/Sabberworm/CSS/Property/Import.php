<?php

declare(strict_types=1);

namespace Sabberworm\CSS\Property;

use Sabberworm\CSS\Value\URL;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Exception\InvalidArgumentException;

/**
 * Class representing an @import rule.
 */
class Import implements AtRule
{
    private URL $oLocation;
    private string $sMediaQuery;
    protected int $iLineNo;
    protected array $aComments;

    public function __construct(URL $oLocation, string $sMediaQuery = '', int $iLineNo = 0)
    {
        $this->oLocation = $oLocation;
        $this->sMediaQuery = $sMediaQuery;
        $this->iLineNo = $iLineNo;
        $this->aComments = [];
    }

    public function getLineNo(): int
    {
        return $this->iLineNo;
    }

    public function setLocation(URL $oLocation): self
    {
        $this->oLocation = $oLocation;
        return $this;
    }

    public function getLocation(): URL
    {
        return $this->oLocation;
    }

    public function __toString(): string
    {
        return $this->render(new OutputFormat());
    }

    public function render(OutputFormat $oOutputFormat): string
    {
        $result = '@import ' . $this->oLocation->render($oOutputFormat);
        if ($this->sMediaQuery !== '') {
            $result .= ' ' . $this->sMediaQuery;
        }
        $result .= ';';

        return $result;
    }

    public function atRuleName(): string
    {
        return 'import';
    }

    public function atRuleArgs(): array
    {
        return [$this->oLocation, $this->sMediaQuery];
    }

    public function addCom
