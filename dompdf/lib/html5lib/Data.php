<?php

declare(strict_types=1);

class HTML5_Data
{
    protected static array $realCodepointTable = [
        0x0000 => 0xFFFD, // REPLACEMENT CHARACTER
        0x000D => 0x000A, // LINE FEED (LF)
        0x0080 => 0x20AC, // EURO SIGN ('€')
        0x0081 => 0x0081, // <control>
        0x0082 => 0x201A, // SINGLE LOW-9 QUOTATION MARK ('‚')
        0x0083 => 0x0192, // LATIN SMALL LETTER F WITH HOOK ('ƒ')
        0x0084 => 0x201E, // DOUBLE LOW-9 QUOTATION MARK ('„')
        0x0085 => 0x2026, // HORIZONTAL ELLIPSIS ('…')
        0x0086 => 0x2020, // DAGGER ('†')
        0x0087 => 0x2021, // DOUBLE DAGGER ('‡')
        0x0088 => 0x02C6, // MODIFIER LETTER CIRCUMFLEX ACCENT ('ˆ')
        0x0089 => 0x2030, // PER MILLE SIGN ('‰')
        0x008A => 0x0160, // LATIN CAPITAL LETTER S WITH CARON ('Š')
        0x008B => 0x2039, // SINGLE LEFT-POINTING ANGLE QUOTATION MARK ('‹')
        0x008C => 0x0152, // LATIN CAPITAL LIGATURE OE ('Œ')
        0x008D => 0x008D, // <control>
        0x008E => 0x017D, // LATIN CAPITAL LETTER Z WITH CARON ('Ž')
        0x008F => 0x008F, // <control>
        0x0090 => 0x0090, // <control>
        0x0091 => 0x2018, // LEFT SINGLE QUOTATION MARK ('‘')
        0x0092 => 0x2019, // RIGHT SINGLE QUOTATION MARK ('’')
        0x0093 => 0x201C, // LEFT DOUBLE QUOTATION MARK ('“')
        0x0094 => 0x201D, // RIGHT DOUBLE QUOTATION MARK ('”')
        0x0095 => 0x2022, // BULLET ('•')
        0x0096 => 0x2013, // EN DASH ('–')
        0x0097 => 0x2014, // EM DASH ('—')
        0x0098 => 0x02DC, // SMALL TILDE ('˜')
        0x0099 => 0x2122, // TRADE MARK SIGN ('™')
        0x009A => 0x0161, // LATIN SMALL LETTER S WITH CARON ('š')
        0x009B => 0x203A, // SINGLE RIGHT-POINTING ANGLE QUOTATION MARK ('›')
        0x009C => 0x0153, // LATIN SMALL LIGATURE OE ('œ')
        0x009D => 0x009D, // <control>
        0x009E => 0x017E, // LATIN SMALL LETTER Z WITH CARON ('ž')
        0x009F => 0x0178, // LATIN CAPITAL LETTER Y WITH DIAERESIS ('Ÿ')
    ];

    protected static array $namedCharacterReferences;

    protected static int $namedCharacterReferenceMaxLength = 12;

    /**
     * Returns the "real" Unicode codepoint of a malformed character
     * reference.
     */
    public static function getRealCodepoint(int $ref): ?int
    {
        return self::$realCodepointTable[$ref] ?? null;
    }

    public static function getNamedCharacterReferences(): array
    {
        if (!self::$namedCharacterReferences) {
            self::$namedCharacterReferences = unserialize(
                file_get_contents(__DIR__ . '/named-character-references.ser')
            );
        }
        return self::$namedCharacterReferences;
    }

    /**
     * Converts a Unicode codepoint to sequence of UTF-8 bytes.
     * @note Shamelessly stolen from HTML Purifier, which is also
     *       shamelessly stolen from Feyd (which is in public domain).
     */
    public static function utf8chr(int $code): string
    {
        if ($code > 0x10FFFF || $code < 0x0 || ($code >= 0xD800 && $code <= 0xDFFF)) {
            // bits are set outside the "valid" range as defined
            // by UNICODE 4.1.0
            return "\xEF\xBF\xBD";
        }

        $y = $z = $w = 0;
        if ($code < 0x80) {
            // regular ASCII character
            $x = $code;
        } else {
            // set up bits for UTF-8
            $x = ($code & 0x3F) | 0x80;
            if ($code < 0x800) {
                $y = (($code & 0x7FF)
