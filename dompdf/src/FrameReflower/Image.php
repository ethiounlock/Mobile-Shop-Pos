<?php

declare(strict_types=1);

namespace Dompdf\FrameReflower;

use Dompdf\Frame;
use Dompdf\FrameDecorator\Block as BlockFrameDecorator;
use Dompdf\FrameDecorator\Image as ImageFrameDecorator;
use Dompdf\Helpers;

/**
 * Image reflower class
 *
 * @package dompdf
 */
class Image extends AbstractFrameReflower
{

    /**
     * Image constructor.
     * @param ImageFrameDecorator $frame
     */
    public function __construct(ImageFrameDecorator $frame)
    {
        parent::__construct($frame);
    }

    /**
     * @param BlockFrameDecorator|null $block
     */
    public function reflow(?BlockFrameDecorator $block = null): void
    {
        $this->_frame->position();

        if ($block) {
            $block->add_frame_to_line($this->_frame);
        }
    }

    /**
     * @return array
     */
    public function get_min_max_width(): array
    {
        $frame = $this->_frame;
        $style = $frame->get_style();

        $width = $this->getSize($frame, 'width');
        $height = $this->getSize($frame, 'height');

        if ($width === 'auto' || $height === 'auto') {
            list($img_width, $img_height) = Helpers::dompdf_getimagesize($frame->get_image_url(), $this->get_dompdf()->getHttpContext());

            if ($width === 'auto' && $height === 'auto') {
                $dpi = $frame->get_dompdf()->getOptions()->getDpi();
                $width = ($img_width * 72) / $dpi;
                $height = ($img_height * 72) / $dpi;
            } elseif ($height === 'auto') {
                $height = ($width / $img_width) * $img_height;
            } else {
                $width = ($height / $img_height) * $img_width;
            }
        }

        // Handle min/max width/height
        if ($style->min_width !== "none" ||
            $style->max_width !== "none" ||
            $style->min_height !== "none" ||
            $style->max_height !== "none"
        ) {
            list( /*$x*/, /*$y*/, $w, $h) = $frame->get_containing_block();

            $min_width = $style->length_in_pt($style->min_width, $w);
            $max_width = $style->length_in_pt($style->max_width, $w);
            $min_height = $style->length_in_pt($style->min_height, $h);
            $max_height = $style->length_in_pt($style->max_height, $h);

            if ($max_width !== "none" && $max_width !== "auto" && $width > (float)$max_width) {
                $width = (float)$max_width;
            }

            if ($min_width !== "none" && $min_width !== "auto" && $width < (float)$min_width) {
                $width = (float)$min_width;
            }

            if ($max_height !== "none" && $max_height !== "auto" && $height > (float)$max_height) {
                $height = (float)$max_height;
            }

            if ($min_height !== "none" && $min_height !== "auto" && $height < (float)$min_height) {
                $height = (float)$min_height;
            }
        }

        $style->width = $width . "pt";
        $style->height = $height . "pt";

        $style->min_width = "none";
        $style->max_width = "none";
        $style->min_height = "none";
        $style->max_height = "none";

        return [$width, $width, "min" => $width, "max" => $width];
    }

    /**
     * @param Frame $f
     * @param string $type
     * @return float
     */
    private function getSize(Frame $f, string $type): float
    {
        $ref_stack = [];
        $result_size = 0.0;

        do {
            $f_style = $f->get_style();
            $current_size = $f_style->$type;

            if (Helpers::is_percent($current_size)) {
                $ref_stack[] = str_replace('%px', '%', $current_size);
            } else {
                if ($current_size !== 'auto') {
                    $result_size = $f_style->length_in_pt($current_size);
                    break;
                }
            }
        } while (($f = $f->get_parent()));

        if (count($ref_stack) > 0) {
            while (($ref = array_pop($ref_stack))) {
                $result_size = $f_style->length_in_pt($ref, $result_size);
            }
        }

        return $result_size;
    }
}
