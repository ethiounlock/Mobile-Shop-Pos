<?php

namespace Sabberworm\CSS\RuleSet;

use Sabberworm\CSS\Comment\Commentable;
use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;
use Sabberworm\CSS\Rule\Rule;
use Sabberworm\CSS\OutputFormat;

/**
 * RuleSet is a generic superclass denoting rules. The typical example for rule sets are declaration block.
 * However, unknown At-Rules (like @font-face) are also rule sets.
 *
 * @var array                   $aRules           Array of rules
 * @var int                     $iLineNo          Line number
 * @var array                   $aComments        Array of comments
 */
abstract class RuleSet implements Renderable, Commentable
{
    private $aRules;
    protected $iLineNo;
    protected $aComments;

    public function __construct(int $iLineNo = 0)
    {
        $this->aRules = [];
        $this->iLineNo = $iLineNo;
        $this->aComments = [];
    }

    /**
     * @param ParserState $oParserState
     * @param RuleSet     $oRuleSet
     *
     * @throws UnexpectedTokenException
     */
    public static function parseRuleSet(ParserState $oParserState, RuleSet $oRuleSet): void
    {
        if ($oParserState === null) {
            throw new \InvalidArgumentException('$oParserState cannot be null');
        }

        while ($oParserState->comes(';')) {
            $oParserState->consume(';');
        }
        while (!$oParserState->comes('}')) {
            $oRule = null;
            if ($oParserState->getSettings()->bLenientParsing) {
                try {
                    $oRule = Rule::parse($oParserState);
                } catch (UnexpectedTokenException $e) {
                    try {
                        $sConsume = $oParserState->consumeUntil(array("\n", ";", '}'), true);
                        if ($oParserState->streql(substr($sConsume, -1), '}')) {
                            $oParserState->backtrack(1);
                        } else {
                            while ($oParserState->comes(';')) {
                                $oParserState->consume(';');
                            }
                        }
                    } catch (UnexpectedTokenException $e) {
                        // We’ve reached the end of the document. Just close the RuleSet.
                        return;
                    }
                }
            } else {
                $oRule = Rule::parse($oParserState);
            }
            if ($oRule) {
                $oRuleSet->addRule($oRule);
            }
        }
        $oParserState->consume('}');
    }

    /**
     * @return int
     */
    public function getLineNo(): int
    {
        return $this->iLineNo;
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

        $this->aRules[$sRule][$iPosition] = $oRule;
    }

    /**
     * Returns all rules matching the given rule name
     *
     * @param (null|string|Rule) $mRule pattern to search for. If null, returns all rules. if the pattern ends with a dash, all rules starting with the pattern are returned as well as one matching the pattern with the dash excluded. passing a Rule behaves like calling getRules($mRule->getRule()).
     * @example $oRuleSet->getRules('font-') //returns an array of all rules either beginning with font- or matching font.
     * @example $oRuleSet->getRules('font') //returns array(0 => $oRule, …) or array().
     *
     * @return Rule[] Rules.
     */
    public function getRules($mRule = null)
    {
        $aResult = [];
        foreach ($this->aRules as $sName => $aRules) {
            // Either no search rule is given or the search rule matches the found rule exactly or the search rule ends in “-” and the found rule starts with the search rule.
            if (!$mRule || $sName === $mRule || (strrpos($mRule, '-') === strlen($mRule) - strlen('-') && (strpos($sName, $mRule) === 0 || $sName === substr($mRule, 0, -1)))) {
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
    public function setRules(array $aRules)
    {
        $this->aRules = [];
        foreach ($aRules as $rule) {
            $this->addRule($rule
