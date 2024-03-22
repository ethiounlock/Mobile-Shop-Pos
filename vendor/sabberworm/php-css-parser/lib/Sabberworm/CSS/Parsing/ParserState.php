<?php

namespace Sabberworm\CSS\Parsing;

use Sabberworm\CSS\Comment\Comment;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;
use Sabberworm\CSS\Settings;
use function array_push;
use function chr;
use function count;
use function intval;
use function is_string;
use function mb_strlen;
use function mb_strpos;
use function mb_strtolower;
use function ord;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function substr;
use function substr_count;

class ParserState
{
    /**
     * @var Settings
     */
    private $oParserSettings;

    /**
     * @var string
     */
    private $sText;

    /**
     * @var array
     */
    private $aText;
    /**
     * @var int
     */
    private $iCurrentPosition;
    /**
     * @var string
     */
    private $sCharset;
    /**
     * @var int
     */
    private $iLength;
    /**
     * @var int
     */
    private $iLineNo;

    /**
     * ParserState constructor.
     * @param string $sText
     * @param Settings $oParserSettings
     * @param int $iLineNo
     */
    public function __construct(string $sText, Settings $oParserSettings, int $iLineNo = 1)
    {
        $this->oParserSettings = $oParserSettings;
        $this->sText = $sText;
        $this->iCurrentPosition = 0;
        $this->iLineNo = $iLineNo;
        $this->setCharset($this->oParserSettings->sDefaultCharset);
    }

    /**
     * @param string $sCharset
     */
    public function setCharset(string $sCharset): void
    {
        $this->sCharset = $sCharset;
        $this->aText = $this->strsplit($this->sText);
        $this->iLength = count($this->aText);
    }

    /**
     * @return string
     */
    public function getCharset(): string
    {
        return $this->sCharset;
    }

    /**
     * @return int
     */
    public function currentLine(): int
    {
        return $this->iLineNo;
    }

    /**
     * @return Settings
     */
    public function getSettings(): Settings
    {
        return $this->oParserSettings;
    }

    /**
     * @param bool $bIgnoreCase
     * @return string
     */
    public function parseIdentifier(bool $bIgnoreCase = true): string
    {
        $sResult = $this->parseCharacter(true);
        if ($sResult === null) {
            throw new UnexpectedTokenException($sResult, $this->peek(5), 'identifier', $this->iLineNo);
        }
        $sCharacter = null;
        while (($sCharacter = $this->parseCharacter(true)) !== null) {
            $sResult .= $sCharacter;
        }
        if ($bIgnoreCase) {
            $sResult = mb_strtolower($sResult, $this->sCharset);
        }
        return $sResult;
    }

    /**
     * @param bool $bIsForIdentifier
     * @return string|null
     */
    public function parseCharacter(bool $bIsForIdentifier = false): ?string
    {
        if ($this->peek() === '\\') {
            if ($bIsForIdentifier && $this->oParserSettings->bLenientParsing && ($this->comes('\0') || $this->comes('\9'))) {
                // Non-strings can contain \0 or \9 which is an IE hack supported in lenient parsing.
                return null;
            }
            $this->consume('\\');
            if ($this->comes('\n') || $this->comes('\r')) {
                return '';
            }
            if (preg_match('/[0-9a-fA-F]/Su', $this->peek()) === 0) {
                return $this->consume(1);
            }
            $sUnicode = $this->consumeExpression('/^[0-9a-fA-F]{1,6}/u', 6);
            if ($this->strlen($sUnicode) < 6) {
                //Consume whitespace after incomplete unicode escape
                if (preg_match('/\s/isSu', $this->peek())) {
                    if ($this->comes('\r\n
