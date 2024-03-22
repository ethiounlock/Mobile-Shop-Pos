<?php

declare(strict_types=1);

namespace FontLib\Glyph;

use FontLib\Font;

/**
 * `glyf` font table.
 *
 * @package php-font-lib
 */
class OutlineSimple extends Outline
{
    public const ON_CURVE       = 0x01;
    public const X_SHORT_VECTOR = 0x02;
    public const Y_SHORT_VECTOR = 0x04;
    public const REPEAT         = 0x08;
    public const THIS_X_IS_SAME = 0x10;
    public const THIS_Y_IS_SAME = 0x20;

    /** @var array<int, array{onCurve: bool, x: float, y: float, endOfContour: bool}> */
    public $points = [];

    /**
     * @param array<int, int>|null $endPtsOfContours
     */
    public function parseData(?array $endPtsOfContours = null): void
    {
        parent::parseData();

        if (!$this->size) {
            return;
        }

        $font = $this->getFont();

        $noc = $this->numberOfContours;

        if ($noc == 0) {
            return;
        }

        $endPtsOfContours = $endPtsOfContours ?? $font->r([self::uint16, $noc]);

        $instructionLength  = $font->readUInt16();
        $this->instructions = $font->r([self::uint8, $instructionLength]);

        $count = $endPtsOfContours[$noc - 1] + 1;

        // Flags
        $flags = [];
        for ($index = 0; $index < $count; $index++) {
            $flags[$index] = $font->readUInt8();

            if ($flags[$index] & self::REPEAT) {
                $repeats = $font->readUInt8();

                for ($i = 1; $i <= $repeats; $i++) {
                    $flags[$index + $i] = $flags[$index];
                }

                $index += $repeats;
            }
        }

        $x = 0;
        $y = 0;
        foreach ($flags as $i => $flag) {
            $this->points[$i] = [
                'onCurve'      => ($flag & self::ON_CURVE) !== 0,
                'endOfContour' => in_array($i, $endPtsOfContours),
            ];

            if ($flag & self::THIS_X_IS_SAME) {
                if ($flag & self::X_SHORT_VECTOR) {
                    $x += $font->readUInt8();
                }
            } else {
                if ($flag & self::X_SHORT_VECTOR) {
                    $x -= $font->readUInt8();
                } else {
                    $x += $font->readInt16();
                }
            }

            $this->points[$i]['x'] = $x;

            if ($flag & self::THIS_Y_IS_SAME) {
                if ($flag & self::Y_SHORT_VECTOR) {
                    $y += $font->readUInt8();
                }
            } else {
                if ($flag & self::Y_SHORT_VECTOR) {
                    $y -= $font->readUInt8();
                } else {
                    $y += $font->readInt16();
                }
            }

            $this->points[$i]['y'] = $y;
        }
    }

    /**
     * @param string $path
     * @return array{onCurve: bool, x: float, y: float, endOfContour: bool}[]
     */
    public function makePoints(string $path): array
    {
        $commands = $this->splitSVGPath($path);

        $points = [];
        $x = 0;
        $y = 0;
        foreach ($commands as $command) {
            switch ($command) {
                case "M":
                    $points[] = [
                        'onCurve'      => true,
                        'x'            => $x = (float)$commands[++$index],
                        'y'            => $y = (float)$commands[++$index],
                        'endOfContour' => false,
                    ];
                    break;

                case "L":
                    $points[] = [
                        'onCurve'      => true,
                        'x'            => $x = (float)$commands[++$index],
                        'y'            => $y = (float)$commands[++$index],
                        'endOfContour' => false,
                    ];
                    break;

                case "Q":
                    $points[] = [
                        'onCurve'      => false,
                        'x'            => $x = (float)$commands[++$index],
                        'y'            => $y = (float)$commands[++$index],
                        'endOfContour' => false,
                    ];
                    $points[] = [
                        'onCurve'      => true,
                        'x'            => $x = (float)$commands[++$index],
                        'y'            => $y = (float)$commands[++$index],
                        '
