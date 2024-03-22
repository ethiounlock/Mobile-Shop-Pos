<?php

declare(strict_types=1);

namespace Dompdf\Adapter;

use Dompdf\Canvas;
use Dompdf\Dompdf;
use Dompdf\Exception;
use Dompdf\Image\Cache;
use Dompdf\PhpEvaluator;
use FontLib\Exception\FontNotFoundException;

/**
 * PDF rendering interface.
 */
class CPdf implements Canvas
{
    /**
     * Dimensions of paper sizes in points.
     *
     * @var array
     */
    private static $PAPER_SIZES = [
        // ...
    ];

    /**
     * The Dompdf object.
     *
     * @var Dompdf
     */
    private $_dompdf;

    /**
     * Instance of Cpdf class.
     *
     * @var Cpdf
     */
    private $_pdf;

    /**
     * PDF width, in points.
     *
     * @var float
     */
    private $_width;

    /**
     * PDF height, in points.
     *
     * @var float
     */
    private $_height;

    /**
     * Current page number.
     *
     * @var int
     */
    private $_pageNumber;

    /**
     * Total number of pages.
     *
     * @var int
     */
    private $_pageCount;

    /**
     * Text to display on every page.
     *
     * @var array
     */
    private $_pageText = [];

    /**
     * Array of pages for accessing after rendering is initially complete.
     *
     * @var array
     */
    private $_pages = [];

    /**
     * Array of temporary cached images to be deleted when processing is complete.
     *
     * @var array
     */
    private $_imageCache = [];

    /**
     * Currently-applied opacity level (0 - 1).
     *
     * @var float
     */
    private $_currentOpacity = 1;

    /**
     * Class constructor.
     *
     * @param string|array $paper The size of paper to use in this PDF.
     * @param string $orientation The orientation of the document.
     * @param Dompdf $dompdf The Dompdf instance.
     */
    public function __construct(string|array $paper = "letter", string $orientation = "portrait", Dompdf $dompdf = null)
    {
        $size = is_array($paper) ? $paper : self::$PAPER_SIZES[strtolower($paper)] ?? self::$PAPER_SIZES["letter"];

        if (strtolower($orientation) === "landscape") {
            [$size[2], $size[3]] = [$size[3], $size[2]];
        }

        if ($dompdf === null) {
            $this->_dompdf = new Dompdf();
        } else {
            $this->_dompdf = $dompdf;
        }

        $this->_pdf = new \Dompdf\Cpdf(
            $size,
            true,
            $dompdf->getOptions()->getFontCache(),
            $dompdf->getOptions()->getTempDir()
        );

        $this->_pdf->addInfo("Producer", sprintf("%s + CPDF", $dompdf->version));
        $time = substr_replace(date('YmdHisO'), '\'', -2, 0) . '\'';
        $this->_pdf->addInfo("CreationDate", "D:$time");
        $this->_pdf->addInfo("ModDate", "D:$time");

        $this->_width = $size[2] - $size[0];
        $this->_height = $size[3] - $size[1];

        $this->_pageNumber = $this->_pageCount = 1;

        $this->_pages = [$this->cpdf()->getFirstPageId()];

        $this->_imageCache = [];
    }

    /**
     * Returns the Dompdf instance.
     *
     * @return Dompdf
     */
    public function dompdf(): Dompdf
    {
        return $this->_dompdf;
    }

    /**
     * Returns the Cpdf instance.
     *
     * @return Cpdf
     */
    public function cpdf(): \Dompdf\Cpdf
    {
        return $this->_pdf;
    }

    // ... (rest of the methods)
}
