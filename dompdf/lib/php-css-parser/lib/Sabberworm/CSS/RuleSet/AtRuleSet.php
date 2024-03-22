<?php

namespace Sabberworm\CSS\RuleSet;

use Sabberworm\CSS\Property\AtRule;

/**
 * A RuleSet constructed by an unknown @-rule. @font-face rules are rendered into AtRuleSet objects.
 */
class AtRuleSet extends RuleSet implements AtRule
{
    /**
     * @var string
     */
    private $sType;

    /**
     * @var string
     */
    private $sArgs;

    /**
     * AtRuleSet constructor.
     * @param string $sType
     * @param string $sArgs
     * @param int $iLineNo
     */
    public function __construct(string $sType, string $sArgs = '', int $iLineNo = 0)
    {
        parent::__construct($iLineNo);
        $this->sType = $sType;
        $this->sArgs = $sArgs;
    }

    /**
     * @return string
     */
    public function atRuleName(): string
    {
        return $this->sType;
    }

    /**
     * @return string
     */
    public function atRuleArgs(): string
    {
        return $this->sArgs;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render(new \Sabberworm\CSS\OutputFormat());
    }

    /**
     * @param \Sabberworm\CSS\OutputFormat $oOutputFormat
     * @return string
     */
    public function render(\Sabberworm\CSS\OutputFormat $oOutputFormat): string
    {
        $sArgs = $this->sArgs;

