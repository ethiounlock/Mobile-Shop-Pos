<?php

declare(strict_types=1);

namespace Svg\Tests;

use PHPUnit\Framework\TestCase;
use Svg\Style;

final class StyleTest extends TestCase
{
    public function testParseColor(): void
    {
        $this->assertSame("none", Style::parseColor("none"));
        $this->assertSame([255, 0, 0], Style::parseColor("RED"));
        $this->assertSame([0, 0, 255], Style::parseColor("blue"));
        $this->assertNull(Style::parseColor("foo"));
        $this->assertSame([0, 0, 0], Style::parseColor("black"));
        $this->assertSame([255, 255, 255], Style::parseColor("white"));
        $this->assertSame([0, 0, 0], Style::parseColor("#000000"));
        $this->assertSame([255, 255, 255], Style::parseColor("#ffffff"));
        $this->assertSame([0, 0, 0], Style::parseColor("rgb(0,0,0)"));
        $this->assertSame([255, 255, 255], Style::parseColor("rgb(255,255,255)"));
        $this->assertSame([0, 0, 0], Style::parseColor("rgb(0, 0, 0)"));
        $this->assertSame([255, 255, 255], Style::parseColor("rgb(255, 255, 255)"));
    }

    public function testFromAttributes(): void
    {
        $style = new Style();

        $attributes = [
            "color" => "blue",
            "fill" => "#fff",
            "stroke" => "none",
        ];

        $style->fromAttributes($attributes);

        $this->assertSame([0, 0, 255], $style->color);
        $this->assertSame([255, 255, 255], $style->fill);
        $this->assertSame("none", $style->stroke);
    }

    public function testConvertSize(): void
    {
        $this->assertSame(1, Style::convertSize(1));
        $this->assertSame(10, Style::convertSize("10px"));
        $this->assertSame(10, Style::convertSize("10pt"));
        $this->assertSame(8, Style::convertSize("80%", 10, 72));
    }
}
