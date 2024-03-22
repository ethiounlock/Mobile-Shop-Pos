<?php

declare(strict_types=1);

namespace App\CSS;

use Sabberworm\CSS\Comment\Commentable;
use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;
use Sabberworm\CSS\Rule\Rule;
use Sabberworm\CSS\Renderable;

/**
 * RuleSet is a generic superclass denoting rules. The typical example for rule sets are declaration block.
 * However, unknown At-Rules (like @font-face) are also rule sets.
 */
abstract class RuleSet implements Renderable, Commentable
{
    private array $aRules;
    private int $iLineNo;
    private array $aComments;

    public function __construct(int $iLineNo = 0)
    {
        $this->aRules = [];
        $this->iLineNo = $iLineNo;
        $this->aComments = [];
    }

    public function addRule(Rule $oRule, Rule $oSibling = null): void
    {
        $sRule = $oRule->getRule();

        if (!isset($this->aRules[$sRule])) {
            $this->aRules[$sRule] = [];
        }

        $iPosition = count($this->aRules[$sRule]);

        if ($oSibling !== null) {
            $iSiblingPos = array_search($oSibling, $this->aRules[$sRule], true);

            if ($iSiblingPos !== false) {
                $iPosition = $iSiblingPos;
            }
        }

        array_splice($this->aRules[$sRule], $iPosition, 0, [$oRule]);
    }

    /**
     * @return int
     */
    public function getLineNo(): int
    {
        return $this->iLineNo;
    }

    /**
     * Returns all rules matching the given rule name
     *
     * @param (null|string|Rule) $mRule pattern to search for. If null, returns all rules. If the pattern ends with a dash, all rules starting with the pattern are returned as well as one matching the pattern with the dash excluded.
     * @return Rule[] Rules.
     */
    public function getRules($mRule = null): array
    {
        if ($mRule instanceof Rule) {
            $mRule = $mRule->getRule();
        }

        $aResult = [];

        foreach ($this->aRules as $sRule => $aRules) {
            if ($mRule === null || $sRule === $mRule || strpos($sRule, $mRule . '-') === 0) {
                $aResult = array_merge($aResult, $aRules);
            }
        }

        return $aResult;
    }

    /**
     * Override all the rules of this set.
     *
     * @param Rule[] $aRules The rules to override with.
     */
    public function setRules(array $aRules): void
    {
        $this->aRules = [];

        foreach ($aRules as $rule) {
            $this->addRule($rule);
        }
    }

    /**
     * Remove a rule from this RuleSet. This accepts all the possible values that getRules() accepts. If given a Rule, it will only remove this particular rule (by identity). If given a name, it will remove all rules by that name.
     *
     * @param (null|string|Rule) $mRule pattern to remove. If $mRule is null, all rules are removed. If the pattern ends in a dash, all rules starting with the pattern are removed as well as one matching the pattern with the dash excluded. Passing a Rule behaves matches by identity.
     */
    public function removeRule($mRule): void
    {
        if ($mRule instanceof Rule) {
            $sRule = $mRule->getRule();

            if (isset($this->aRules[$sRule])) {
                foreach ($this->aRules[$sRule] as $iKey => $oRule) {
                    if ($oRule === $mRule) {
                        unset($this->aRules[$sRule][$iKey]);
                    }
                }
            }
        } else {
            foreach ($this->aRules as $sName => $aRules) {
                if ($mRule === null || $sName === $mRule || strpos($sName, $mRule . '-') === 0) {
                    unset($this->aRules[$sName]);
                }
            }
        }
    }

    public function __toString()
    {
        return $this->render(new \Sabberworm\CSS\OutputFormat());
    }

    public function render(
