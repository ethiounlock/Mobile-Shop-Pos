<?php

namespace FontLib\Table\Type;

use FontLib\Table\Table;

/**
 * `hmtx` font table.
 *
 * @package php-font-lib
 */
class Hmtx extends Table
{
    /**
     * @var int[]
     */
    protected array $data = [];

    /**
     * @var int
     */
    protected int $numOfLongHorMetrics = 0;

    /**
     * @var int
     */
    protected int $numGlyphs = 0;

    /**
     * {@inheritdoc}
     */
    protected function _parse(): void
    {
        $font = $this->getFont();
        $offset = $font->pos();

        $this->numOfLongHorMetrics = $font->getData('hhea', 'numOfLongHorMetrics');
        $this->numGlyphs = $font->getData('maxp', 'numGlyphs');

        $font->seek($offset);

        $metrics = $font->readUInt1
