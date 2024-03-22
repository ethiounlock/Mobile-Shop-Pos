<?php

namespace Sabberworm\CSS\Value;

use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;
use Sabberworm\CSS\RuleValueList;
use Sabberworm\CSS\Value\Value;

class CalcFunction extends CSSFunction
{
    const T_OPERAND = 1;
    const T_OPERATOR = 2;

    public static function parse(ParserState $oParserState): self
    {
        $aOperators = ['+', '-', '*', '/'];
        $sFunction = trim($oParserState->consumeUntil('(', false, true));
        $oCalcList = new CalcRuleValueList($oParserState->currentLine());
        $oList = new RuleValueList(',', $oParserState->currentLine());
        $iNestingLevel = 0;
        $iLastComponentType = null;

        while (!$oParserState->comes(')') || $iNestingLevel > 0) {
            $oParserState->consumeWhiteSpace();

            if ($oParserState->comes('(')) {
                $iNestingLevel++;
                $oCalcList->addListComponent($oParserState->consume(1));
                continue;
            }

            if ($oParserState->comes(')')) {
                $iNestingLevel--;
                $oCalcList->addListComponent($oParserState->consume(1));
                continue;
            }

            if ($iLastComponentType !== self::T_OPERAND) {
                $oVal = Value::parsePrimitiveValue($oParserState);
                $oCalcList->addListComponent($oVal);
                $iLastComponentType = self::T_OPERAND;
            } else {
                if (in_array($oParserState->peek(), $aOperators)) {
                    if (in_array($oParserState->peek(1, -1), ['-', '+']) && ($oParserState->peek(2) !== ' ')) {
                        throw new UnexpectedTokenException(
                            sprintf(
                                'Expected space after "%s"',
                                $oParserState->peek(1, -1)
                            ),
                            $oParserState->peek(1, -1) . $oParserState->peek(2),
                            'literal',
                            $oParserState->currentLine()
                        );
                    }

                    $oCalcList->addListComponent($oParserState->consume(1));
                    $iLastComponentType = self::T_OPERATOR;
                } else {
                    throw new UnexpectedTokenException(
                        sprintf(
                            'Next token was expected to be an operator. Instead "%s" was found.',
                            $oParserState->peek()
                        ),
                        '',
                        'custom',
                        $oParserState->currentLine()
                    );
                }
            }
        }

        $oList->addListComponent($oCalcList);
        $oParserState->consume(')');

        return new self($sFunction, $oList, ',', $oParserState->currentLine());
    }
