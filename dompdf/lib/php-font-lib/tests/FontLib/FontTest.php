<?php

namespace FontLib\Tests;

use FontLib\Font;
use PHPUnit\Framework\TestCase;

class FontTest extends TestCase
{
    /**
     * @var Font
     */
    private $font;

    protected function setUp(): void
    {
        $this->font = Font::load('sample-fonts/IntelClear-Light.ttf');
    }

    protected function tearDown(): void
    {
        $this->font = null;
    }

    /**
     * @expectedException \Fontlib\Exception\FontNotFoundException
     * @expectedExceptionMessage File 'non-existing/font.ttf' not found.
     */
    public function testLoadFileNotFound()
    {
        Font::load('non-existing/font.ttf');
    }

    /**
     * @group ttf
     * @dataProvider ttfFonts
     */
    public function testLoadTTFAndOTFFontSuccessfully($fontFile)
    {
        $font = Font::load($fontFile);

        $this->assertInstanceOf('FontLib\TrueType\File', $font);
    }

    /**
     * @group cmap
     * @dataProvider cmapFormats
     */
    public function testCmapFormat($format, $startCodeCount, $endCodeCount, $glyphIndexArrayCount)
    {
        $this->font->parse();

        $cmapTable = $this->font->getData("cmap", "subtables");

        $cmapFormatTable = $cmapTable[0];

        $this->assertEquals($format, $cmapFormatTable['format']);
        $this->assertEquals($startCodeCount, $cmapFormatTable['segCount']);
        $this->assertEquals($startCodeCount, count($cmapFormatTable['startCode']));
