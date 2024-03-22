<?php

namespace FontLib\Table\Type;

use FontLib\Table\Table;

/**
 * `hmtx` font table.
 *
 * @package php-font-lib
 */
class hmtx extends Table
{
    /**
     * @var int
     */
    protected $numOfLongHorMetrics;

    /**
     * @var int
     */
    protected $numGlyphs;

    /**
     * @var array
     */
    protected $data;

    /**
     * {@inheritdoc}
     */
    protected function _parse()
    {
        $font = $this->getFont();
        $offset = $font->pos();

        $this->numOfLongHorMetrics = $font->getData("hhea", "numOfLongHorMetrics");
        $this->numGlyphs = $font->getData("maxp", "numGlyphs");

        $font->seek($offset);

        $metrics = $font->readUInt16Many($this->numOfLongHorMetrics * 2);
        for ($gid = 0, $mid = 0; $gid < $this->numOfLongHorMetrics; $gid++) {
            $advanceWidth = $metrics[$mid] ?? 0;
            $mid += 1;
            $leftSideBearing = $metrics[$mid] ?? 0;
            $mid += 1;
            $this->data[$gid] = [$advanceWidth, $leftSideBearing];
        }

        if ($this->numOfLongHorMetrics < $this->numGlyphs) {
            $lastWidth = end($this->data);
            $this->data = array_pad($this->data, $this->numGlyphs, $lastWidth);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _encode()
    {
        $font = $this->getFont();
        $subset = $font->getSubset();
        $data = $this->data;

        $length = 0;

        foreach ($subset as $gid) {
            $length += $font->writeUInt16($data[$gid][0]);
            $length += $font->
