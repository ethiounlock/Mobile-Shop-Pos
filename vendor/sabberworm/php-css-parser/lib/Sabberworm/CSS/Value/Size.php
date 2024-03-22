<?php

namespace Sabberworm\CSS\Value;

use Sabberworm\CSS\Parsing\ParserState;

class Size extends PrimitiveValue
{
    const ABSOLUTE_SIZE_UNITS = 'px/cm/mm/mozmm/in/pt/pc/vh/vw/vm/vmin/vmax/rem';
    const RELATIVE_SIZE_UNITS = '%/em/ex/ch/fr';
    const NON_SIZE_UNITS = 'deg/grad/rad/s/ms/turns/Hz/kHz';

    private static $SIZE_UNITS = null;

    private float $size;
    private ?string $unit;
    private bool $isColorComponent;

    public function __construct(float $size, ?string $unit = null, bool $isColorComponent = false, int $lineNo = 0)
    {
        parent::__construct($lineNo);
        $this->size = $size;
        $this->unit = $unit;
        $this->isColorComponent = $isColorComponent;
    }

    public static function parse(ParserState $oParserState, bool $isColorComponent = false): self
    {
        $size = '';
        if ($oParserState->comes('-')) {
            $size .= $oParserState->consume('-');
        }
        while (is_numeric($oParserState->peek()) || $oParserState->comes('.')) {
            if ($oParserState->comes('.')) {
                $size .= $oParserState->consume('.');
            } else {
                $size .= $oParserState->consume(1);
            }
        }

        $unit = null;
        $aSizeUnits = self::getSizeUnits();
        foreach ($aSizeUnits as $length => &$values) {
            $key = strtolower($oParserState->peek($length));
            if (array_key_exists($key, $values)) {
                if (($unit = $values[$key]) !== null) {
                    $oParserState->consume($length);
                    break;
                }
            }
        }

        return new self(floatval($size), $unit, $isColorComponent, $oParserState->currentLine());
    }

    private static function getSizeUnits(): array
    {
        if (self::$SIZE_UNITS === null) {
            self::$SIZE_UNITS = [];
            foreach (explode('/', self::ABSOLUTE_SIZE_UNITS . '/' . self::RELATIVE_SIZE_UNITS . '/' . self::NON_SIZE_UNITS) as $val) {
                $size = strlen($val);
                if (!isset(self::$SIZE_UNITS[$size])) {
                    self::$SIZE_UNITS[$size] = [];
                }
                self::$SIZE_UNITS[$size][strtolower($val)] = $val;
            }

            // FIXME: Should we not order the longest units first?
            ksort(self::$SIZE_UNITS, SORT_NUMERIC);
        }

        return self::$SIZE_UNITS;
    }

    public function setUnit(?string $unit): void
    {
        $this->unit = $unit;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setSize(float $size): void
    {
        $this->size = $size;
    }

    public function getSize(): float
    {
        return $this->size;
    }

    public function isColorComponent(): bool
    {
        return $this->isColorComponent;
    }

    /**
     * Returns whether the number stored in this Size really represents a size (as in a length of something on screen).
     * @return false if the unit an angle, a duration, a frequency or the number is a component in a Color object.
     */
    public function isSize(): bool
    {
        if (in_array($this->unit, explode('/', self::NON
