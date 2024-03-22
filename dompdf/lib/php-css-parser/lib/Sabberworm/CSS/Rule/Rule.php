<?php

namespace Sabberworm\CSS\Rule;

use Sabberworm\CSS\Comment\Commentable;
use Sabberworm\CSS\Comment\Comments;
use Sabberworm\CSS\Lexer;
use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Renderable;
use Sabberworm\CSS\Value\RuleValueList;
use Sabberworm\CSS\Value\Value;

/**
 * RuleSets contains Rule objects which always have a key and a value.
 * In CSS, Rules are expressed as follows: “key: value[0][0] value[0][1], value[1][0] value[1][1];”
 */
class Rule implements Renderable, Commentable
{
    private string $sRule;
    private RuleValueList $mValue;
    private Important $oImportant;
    private IeHack $oIeHack;
    protected int $iLineNo;
    private Comments $oComments;

    public function __construct(string $sRule, int $iLineNo = 0)
    {
        $this->sRule = $sRule;
        $this->mValue = new RuleValueList(' ', $iLineNo);
        $this->oImportant = new Important();
        $this->oIeHack = new IeHack();
        $this->iLineNo = $iLineNo;
        $this->oComments = new Comments();
    }

    public static function parse(Lexer $oLexer, ParserState $oParserState): self
    {
        $oRule = new self($oLexer->consumeIdentifier(), $oParserState->currentLine());
        $oRule->setComments($oParserState->consumeWhiteSpace());
        $oLexer->consume(':');
        $oValue = Value::parseValue($oLexer, self::listDelimiterForRule($oRule->getRule()));
        $oRule->setValue($oValue);
        if ($oParserState->getSettings()->bLenientParsing) {
            while ($oLexer->comes('\\')) {
                $oLexer->consume('\\');
                $oRule->addIeHack($oLexer->consume());
                $oLexer->consumeWhiteSpace();
            }
        }
        $oLexer->consumeWhiteSpace();
        if ($oLexer->comes('!')) {
            $oLexer->consume('!');
            $oLexer->consumeWhiteSpace();
            $oLexer->consume('important');
            $oRule->setIsImportant(true);
        }
        $oLexer->consumeWhiteSpace();
        while ($oLexer->comes(';')) {
            $oLexer->consume(';');
        }
        $oLexer->consumeWhiteSpace();

        return $oRule;
    }

    private static function listDelimiterForRule(string $sRule): array
    {
        if (preg_match('/^font($|-)/', $sRule)) {
            return array(',', '/', ' ');
        }
        return array(',', ' ', '/');
    }

    /**
     * @return int
     */
    public function getLineNo(): int
    {
        return $this->iLineNo;
    }

    public function setRule(string $sRule): void
    {
        $this->sRule = $sRule;
    }

    public function getRule(): string
    {
        return $this->sRule;
    }

    public function getValue(): RuleValueList
    {
        return $this->mValue;
    }

    public function setValue(RuleValueList $mValue): void
    {
        $this->mValue = $mValue;
    }

    public function addValue(Value $mValue, string $sType = ' '): void
    {
        if (!$this->mValue instanceof RuleValueList || $this->mValue->getListSeparator() !== $sType) {
            $mCurrentValue = $this->mValue;
            $this->mValue = new RuleValueList($sType, $this->iLineNo);
            if ($mCurrentValue) {
                $this->mValue->addListComponent($mCurrentValue);
            }
        }
        $this->mValue->addListComponent($mValue);
    }

    public function setIeHack(array $aModifiers): void
    {
        $this->oIeHack = new IeHack($aModifiers);
    }

    public function getIeHack(): IeHack
    {
        return $this->oIeHack;
    }

    public function addIeHack(string $iModifier): void
    {

