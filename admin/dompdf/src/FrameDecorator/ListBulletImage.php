<?php

declare(strict_types=1);

namespace Dompdf\FrameDecorator;

use Dompdf\Dompdf;
use Dompdf\Frame;
use Dompdf\Image\Cache;
use Dompdf\Helpers;
use Exception;

/**
 * Decorates frames for list bullets with custom images
 *
 * @package dompdf
 */
class ListBulletImage extends AbstractFrameDecorator
{

    /**
     * @var Image
     */
    protected $_img;

    /**
     * @var int
     */
    protected $_width;

    /**
     * @var int
     */
    protected $_height;

    /**
     * Class constructor
     *
     * @param Frame $frame   the bullet frame to decorate
     * @param Dompdf $dompdf the document's dompdf object
     */
    public function __construct(Frame $frame, Dompdf $dompdf)
    {
        $style = $frame->get_style();
        $url = $style->list_style_image;
        $frame->get_node()->setAttribute("src", $url);
        $this->_img = new Image($frame, $dompdf);
        parent::__construct($this->_img, $dompdf);

        if (Cache::is_broken($this->_img->get_image_url())) {
            $width = 0;
            $height = 0;
        } else {
            [$width, $height] = Helpers::dompdf_getimagesize($this->_img->get_image_url(), $dompdf->getHttpContext());
        }

        $dpi = $this->_dompdf->getOptions()->getDpi();
        $this->_width = ((float)rtrim($width, "px") * 72) / $dpi;
        $this->_height = ((float)rtrim($height, "px") * 72) / $dpi;

        if ($style->min_height !== null && $style->min_height < $this->_height) {
            $style->min_height = $this->_height;
        }
        $style->height = "auto";
    }

    /**
     * Return the bullet's width
     *
     * @return int
     */
    public function get_width(): int
    {
        return $this->_frame->get_style()->font_size * ListBullet::BULLET_SIZE + 2 * ListBullet::BULLET_PADDING;
    }

    /**
     * Return the bullet's height
     *
     * @return int
     * @throws Exception
     */
    public function get_height(): int
    {
        if ($this->_height == 0) {
            $style = $this->_frame->get_style();

            if ($style->list_style_type === "none") {
                return 0;
            }

            throw new Exception("List bullet image height is not set.");
        } else {
            return $this->_height;
        }
    }

    /**
     * Override get_margin_width
     *
     * @return int
     */
    public function get_margin_width(): int
    {
        if ($this->_frame->get_style()->list_style_position === "outside" || $this->_width == 0) {
            return 0;
        }

        return $this->_width + 2 * ListBullet::BULLET_PADDING;
    }

    /**
     * Override get_margin_height()
     *
     * @return int
     */
    public function get_margin_height(): int
    {
        return $this->_height + 2 * ListBullet::BULLET_PADDING;
    }

    /**
     * Return image url
     *
     * @return string
     */
    public function get_image_url(): string
    {
        return $this->_img->get_image_url();
    }

}
