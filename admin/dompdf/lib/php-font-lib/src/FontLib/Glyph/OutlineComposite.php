<?php

declare(strict_types=1);

namespace FontLib\Glyph;

use FontLib\Font;

/**
 * Composite glyph outline
 *
 * @package php-font-lib
 */
class OutlineComposite extends Outline
{
    const ARG_1_AND_2_ARE_WORDS = 0x0001;
    const ARGS_ARE_XY_VALUES = 0x0002;
    const ROUND_XY_TO_GRID = 0x0004;
    const WE_HAVE_A_SCALE = 0x0008;
    const MORE_COMPONENTS = 0x0020;
    const WE_HAVE_AN_X_AND_Y_SCALE = 0x0040;
    const WE_HAVE_A_TWO_BY_TWO = 0x0080;
    const WE_HAVE_INSTRUCTIONS = 0x0100;
    const USE_MY_METRICS = 0x0200;
    const OVERLAP_COMPOUND = 0x0400;

    /**
     * @var OutlineComponent[]
     */
    public array $components = [];

    /**
     * Get glyph IDs
     *
     * @return int[]
     */
    public function getGlyphIDs(): array
    {
        if (empty($this->components)) {
            $this->parseData();
        }

        $glyphIDs = [];
        foreach ($this->components as $component) {
            $glyphIDs[] = $component->glyphIndex;

            $glyph = $this->table->data[$component->glyphIndex] ?? null;

            if ($glyph !== $this) {
                $glyphIDs = array_merge($glyphIDs, $glyph->getGlyphIDs());
            }
        }

        return $glyphIDs;
    }

    /**
     * Parse data
     */
    public function parseData(): void
    {
        parent::parseData();

        $font = $this->getFont();

        do {
            $flags = $font->readUInt16();
            $glyphIndex = $font->readUInt16();

            $a = 1.0;
            $b = 0.0;
            $c = 0.0;
            $d = 1.0;
            $e = 0.0;
            $f = 0.0;

            $point_compound = null;
            $point_component = null;

            $instructions = null;

            if ($flags & self::ARG_1_AND_2_ARE_WORDS) {
                if ($flags & self::ARGS_ARE_XY_VALUES) {
                    $e = $font->readInt16();
                    $f = $font->readInt16();
                } else {
                    $point_compound = $font->readUInt16();
                    $point_component = $font->readUInt16();
                }
            } else {
                if ($flags & self::ARGS_ARE_XY_VALUES) {
                    $e = $font->readInt8();
                    $f = $font->readInt8();
                } else {
                    $point_compound = $font->readUInt8();
                    $point_component = $font->readUInt8();
                }
            }

            if ($flags & self::WE_HAVE_A_SCALE) {
                $a = $d = $font->readInt16();
            } elseif ($flags & self::WE_HAVE_AN_X_AND_Y_SCALE) {
                $a = $font->readInt16();
                $d = $font->readInt16();
            } elseif ($flags & self::WE_HAVE_A_TWO_BY_TWO) {
                $a = $font->readInt16();
                $b = $font->readInt16();
                $c = $font->readInt16();
                $d = $font->readInt16();
            }

            //if ($flags & self::WE_HAVE_INSTRUCTIONS) {
            //
            //}

            $component = new OutlineComponent();
            $component->flags = $flags;
            $component->glyphIndex = $glyphIndex;
            $component->a = $a;
            $component->b = $b;
            $component->c = $c;
            $component->d = $d;
            $component->e = $e;
            $component->f = $f;
            $component->point_compound = $point_compound;
            $component->point_component = $point_component;
            $component->instructions = $instructions;

            $this->components[] = $component;
        } while ($flags & self::MORE_COMPONENTS);
    }

    /**
     * Encode
     *
     * @return int
     */
    public function encode(): int
    {
        $font = $this->getFont();

        $gids = $font->getSubset();

        $size = $font->writeInt16(-1);
        $size += $font->writeFWord($this->xMin);
        $size += $font->writeFWord($this->yMin);
        $size += $font->writeFWord($this->xMax);
        $size += $font->writeFWord($this->yMax);

        foreach ($this->components as $i => $component) {
            $flags = 0;
            if ($component->point_component === null && $component->point_compound === null) {
                $flags |= self::ARGS_ARE_XY
