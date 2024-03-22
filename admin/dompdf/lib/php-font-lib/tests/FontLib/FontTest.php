<?php

namespace FontLib\Tests;

use FontLib\Font;
use PHPUnit\Framework\TestCase;

class FontTest extends TestCase
{
    /**
     * @var Font
     */
    private static $font;

    public static function setUpBeforeClass(): void
    {
        self::$font = Font::load('sample-fonts/IntelClear-Light.ttf');
    }

    public static function tearDownAfterClass(): void
    {
        self::$font = null;
    }

    protected function setUp(): void
    {
        // Create a new font object for each test method.
        $this->font = Font::load('sample-fonts/IntelClear-Light.ttf');
    }

    protected function tearDown(): void
    {
        // Delete the font object after each test method.
        $this->font = null;
    }

    /**
     * @expectedException \Fontlib\Exception\FontNotFoundException
     * @covers \FontLib\Font::load
     */
    public function testLoadFileNotFound()
    {
        Font::load('non-existing/font.ttf');
    }

    /**
     * @dataProvider fontFiles
     * @covers \FontLib\Font::load
     */
    public function testLoadFont($filePath)
    {
        $font = Font::load($filePath);

        $this->assertInstanceOf('FontLib\Font', $font);
    }

    public function fontFiles()
    {
        return [
            ['sample-fonts/IntelClear-Light.ttf'],
            ['sample-fonts/NotoSansShavian-Regular.tt
