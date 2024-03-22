<?php

declare(strict_types=1);

namespace Dompdf\Renderer;

use Dompdf\Adapter\CPDF;
use Dompdf\Frame;
use Dompdf\FrameDecorator\Text;

/**
 * Renders text frames
 *
 * @package dompdf
 */
class Text extends AbstractRenderer
{
    /** Thickness of underline. Screen: 0.08, print: better less, e.g. 0.04 */
    public const DECO_THICKNESS = 0.02;

    //Tweaking if $base and $descent are not accurate.
    //Check method_exists( $this->_canvas, "get_cpdf" )
    //- For cpdf these can and must stay 0, because font metrics are used directly.
    //- For other renderers, if different values are wanted, separate the parameter sets.
    //  But $size and $size-$height seem to be accurate enough

    /** Relative to bottom of text, as fraction of height */
    public const UNDERLINE_OFFSET = 0.0;

    /** Relative to top of text */
    public const OVERLINE_OFFSET = 0.0;

    /** Relative to centre of text. */
    public const LINETHROUGH_OFFSET = 0.0;

    /** How far to extend lines past either end, in pt */
    public const DECO_EXTENSION = 0.0;

    /**
     * @param Text $frame
     */
    public function render(Frame $frame): void
    {
        $text = $frame->get_text();
        if (trim($text) === "") {
            return;
        }

        $style = $frame->get_style();
        [$x, $y] = $frame->get_position();
        $cb = $frame->get_containing_block();

        if (($ml = $style->margin_left) === "auto" || $ml === "none") {
            $ml = 0;
        }

        if (($pl = $style->padding_left) === "auto" || $pl === "none") {
            $pl = 0;
        }

        if (($bl = $style->border_left_width) === "auto" || $bl === "none") {
            $bl = 0;
        }

        $x += (float)$style->length_in_pt([$ml, $pl, $bl], $cb["w"]);

        $font = $style->font_family;
        $size = $style->font_size;
        $frame_font_size = $frame->get_dompdf()->getFontMetrics()->getFontHeight($font, $size);
        $word_spacing = $frame->get_text_spacing() + (float)$style->length_in_pt($style->word_spacing);
        $char_spacing = (float)$style->length_in_pt($style->letter_spacing);
        $width = $style->width;

        /*$text = str_replace(
          array("{PAGE_NUM}"),
          array($this->_canvas->get_page_number()),
          $text
        );*/

        $this->_canvas->text($x, $y, $text,
            $font, $size,
            $style->color, $word_spacing, $char_spacing);

        $line = $frame->get_containing_line();

        // FIXME Instead of using the tallest frame to position,
        // the decoration, the text should be well placed
        if (false && $line->tallest_frame) {
            $base_frame = $line->tallest_frame;
            $style = $base_frame->get_style();
            $size = $style->font_size;
        }

        $line_thickness = $size * self::DECO_THICKNESS;
        $underline_offset = $size * self::UNDERLINE_OFFSET;
        $overline_offset = $size * self::OVERLINE_OFFSET;
        $linethrough_offset = $size * self::LINETHROUGH_OFFSET;
        $underline_position = -0.08;

        if ($this->_canvas instanceof CPDF) {
            $cpdf_font = $this->_canvas->get_cpdf()?->fonts[$style->font_family] ?? null;

            if ($cpdf_font && isset($cpdf_font["UnderlinePosition"])) {
                $underline_position = $cpdf_font["UnderlinePosition"] / 1000;
            }

            if ($cpdf_font && isset($cpdf_font["UnderlineThickness"])) {
                $line_thickness = $size * ($cpdf_font["UnderlineThickness"] / 1000);
            }
        }

        $descent = $size * $underline_position;
        $base = $frame_font_size;

        // Handle text decoration:
        // http://www.w3.org/TR/CSS21/text.html#propdef-text-decoration

        // Draw all applicable text-decorations.  Start with the root and work our way down.
        $p = $frame;
        $stack = [];
        while ($p = $p->get_parent()) {
            $stack[] = $p;
        }

        while (isset($stack[0])) {
            $f = array_pop($stack);

            if (($text_deco = $f->get_style()->text_decoration) === "none") {
                continue;
            }

            $deco_y = $y; //$line->y;
            $color = $f->get_style()->color;

            switch ($
