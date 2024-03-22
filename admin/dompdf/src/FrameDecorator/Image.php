<?php

declare(strict_types=1);

namespace Dompdf\FrameDecorator;

use Dompdf\Dompdf;
use Dompdf\Frame;
use Dompdf\Image\Cache;

/**
 * Decorates frames for image layout and rendering
 *
 * @package dompdf
 */
class Image extends AbstractFrameDecorator
{
    /**
     * @var string
     */
    protected string $_image_url;

    /**
     * @var string|null
     */
    protected ?string $_image_msg;

    /**
     * Class constructor
     *
     * @param Frame $frame
     * @param Dompdf $dompdf
     */
    public function __construct(Frame $frame, Dompdf $dompdf)
    {
        parent::__construct($frame, $dompdf);

        $url = $frame->get_node()->getAttribute("src");

        if ($dompdf->getOptions()->getDebugPng()) {
            print '[__construct ' . $url . ']';
        }

        list($this->_image_url, /*$type*/, $this->_image_msg) = Cache::resolve_url(
            $url,
            $dompdf->getProtocol(),
            $dompdf->getBaseHost(),
            $dompdf->getBasePath(),
            $dompdf
        );

        if (Cache::is_broken($this->_image_url) && $alt = $frame->get_node()->getAttribute("alt")) {
            $style = $frame->get_style();
            $style->width = (4 / 3) * $dompdf->getFontMetrics()->getTextWidth($alt, $style->font_family, $style->font_size, $style->word_spacing);
            $style->height = $dompdf->getFontMetrics()->getFontHeight($style->font_family, $style->font_size);
        }
    }

    /**
     * Return the image's url
     *
     * @return string The url of this image
     */
    public function get_image_url(): string
    {
        return $this->_image_url;

