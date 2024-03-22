<?php

declare(strict_types=1);

namespace Dompdf\Adapter;

use Dompdf\Canvas;
use Dompdf\Dompdf;
use Dompdf\Helpers;
use Dompdf\Exception;
use Dompdf\Image\Cache;
use Dompdf\PhpEvaluator;
use FontLib\Exception\FontNotFoundException;
use function array_unshift;
use function call_user_func_array;
use function compact;
use function count;
use function error_log;
use function file_exists;
use function Helpers\dompdf_getimagesize;
use function Helpers\record_warnings;
use function is_array;
use function ltrim;
use function mb_strlen;
use function mb_strtolower;
use function rawurldecode;
use function substr_replace;
use function tempnam;
use function unlink;

/**
 * PDF rendering interface.
 */
class CPDF implements Canvas
{
    /**
     * @var Dompdf
     */
    private $_dompdf;

    /**
     * @var \Dompdf\Cpdf
     */
    private $_pdf;

    /**
     * @var float
     */
    private $_width;

    /**
     * @var float
     */
    private $_height;

    /**
     * @var int
     */
    private $_page_number;

    /**
     * @var int
     */
    private $_page_count;

    /**
     * @var array
     */
    private $_page_text;

    /**
     * @var array
     */
    private $_pages;

    /**
     * @var array
     */
    private $_image_cache;

    /**
     * @var float
     */
    private $_current_opacity;

    /**
     * CPDF constructor.
     *
     * @param string|array $paper
     * @param string $orientation
     * @param Dompdf|null $dompdf
     */
    public function __construct(string|array $paper = "letter", string $orientation = "portrait", ?Dompdf $dompdf = null)
    {
        $this->_dompdf = $dompdf ?? new Dompdf();

        $size = is_array($paper) ? $paper : self::$PAPER_SIZES[mb_strtolower($paper)] ?? self::$PAPER_SIZES["letter"];

        if (mb_strtolower($orientation) === "landscape") {
            [$size[2], $size[3]] = [$size[3], $size[2]];
        }

        $this->_pdf = new \Dompdf\Cpdf(
            $size,
            true,
            $this->_dompdf->getOptions()->getFontCache(),
            $this->_dompdf->getOptions()->getTempDir()
        );

        $this->_pdf->addInfo("Producer", sprintf("%s + CPDF", $this->_dompdf->version));
        $time = substr_replace(date('YmdHisO'), '\'', -2, 0) . '\'';
        $this->_pdf->addInfo("CreationDate", "D:$time");
        $this->_pdf->addInfo("ModDate", "D:$time");

        $this->_width = $size[2] - $size[0];
        $this->_height = $size[3] - $size[1];

        $this->_page_number = $this->_page_count = 1;
        $this->_page_text = [];

        $this->_pages = [$this->_pdf->getFirstPageId()];

        $this->_image_cache = [];
    }

    // ... (rest of the code)
}
