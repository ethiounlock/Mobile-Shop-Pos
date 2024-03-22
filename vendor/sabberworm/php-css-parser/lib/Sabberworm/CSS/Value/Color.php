<?php

namespace Sabberworm\CSS\Value;

use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Exception\InvalidCSSException;

class Color extends CSSFunction
{
    /**
     * @var array
     */
    private $aComponents;

    /**
     * Color constructor.
     * @param array $aColor
     * @param int $iLineNo
     */
    public function __construct(array $aColor, int $iLineNo = 0)
    {
        $sName = implode('', array_keys($aColor));
        parent::__construct($sName, $aColor, ',', $iLineNo);
        $this->aComponents = $aColor;
    }

    /**
     * @param ParserState $oParserState
     * @return static
     * @throws InvalidCSSException
     */
    public static function parse(ParserState $oParserState): self
    {
        $aColor = [];
        if ($oParserState->comes('#')) {
            $oParserState->consume('#');
            $sValue = $oParserState->parseIdentifier(false);
            if ($oParserState->strlen($sValue) === 3 || $oParserState->strlen($sValue) === 4 || $oParserState->strlen($sValue) === 8) {
                $aColor = self::parseHashColor($sValue);
            }
        } else {
            $sColorMode = $oParserState->parseIdentifier(true);
            $oParserState->consumeWhiteSpace();
            $oParserState->consume('(');
            $iLength = $oParserState->strlen($sColorMode);
            for ($i = 0; $i < $iLength; ++$i) {
                $oParserState->consumeWhiteSpace();
                $aColor[$sColorMode[$i]] = Size::parse($oParserState, true);
                $oParserState->consumeWhiteSpace();
                if ($i < ($iLength - 1)) {
                    $oParserState->consume(',');
                }
            }
            $oParserState->consume(')');
        }

        if (empty($aColor)) {
            throw new InvalidCSSException("Invalid color format: {$oParserState->current()}", $oParserState->currentLine());
        }

        return new self($aColor, $oParserState->currentLine());
    }

    /**
     * @param string $sValue
     * @return array
     */
    private static function parseHashColor(string $sValue): array
    {
        $aColor = [];
        $iLength = $oParserState->strlen($sValue);
        if ($iLength === 3) {
            $sValue = $sValue[0] . $sValue[0] . $sValue[1] . $sValue[1] . $sValue[2] . $sValue[2];
            $aColor = [
                'r' => new Size(hexdec($sValue[0] . $sValue[1]), null, true, $oParserState->currentLine()),
                'g' => new Size(hexdec($sValue[2] . $sValue[3]), null, true, $oParserState->currentLine()),
                'b' => new Size(hexdec($sValue[4] . $sValue[5]), null, true, $oParserState->currentLine()),
            ];
        } else if ($iLength === 4) {
            $sValue = $sValue[0] . $sValue[0] . $sValue[1] . $sValue[1] . $sValue[2] . $sValue[2] . $sValue[3] . $sValue[3];
            $aColor = [
                'r' => new Size(hexdec($sValue[0] . $sValue[1]), null, true, $oParserState->currentLine()),
                'g' => new Size(hexdec($sValue[2] . $sValue[3]), null, true, $oParserState->currentLine()),
                'b' => new Size(hexdec($sValue[4] . $sValue[5]), null, true, $oParserState->currentLine()),
                'a' => new Size(round(self::mapRange(hexdec($sValue[6] . $sValue[7]), 0, 255, 0, 1), 2), null, true, $oParserState->currentLine()),
            ];
        } else if ($iLength === 8) {
            $aColor = [
                'r' => new Size(hexdec($sValue[0] . $sValue[1]), null, true, $oParserState->currentLine()),
                'g' => new Size(hexdec($sValue[2] . $sValue[3]), null, true, $oParserState->currentLine()),
                'b' => new Size(hexdec($sValue[4] . $sValue[5]), null, true, $oParserState->currentLine()),
                'a' => new Size(round(self::mapRange(hexdec($sValue[6] . $sValue[7]), 0, 255, 0, 1), 2
