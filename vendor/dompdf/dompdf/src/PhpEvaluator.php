<?php

declare(strict_types=1);

namespace Dompdf;

use Dompdf\Exception;

/**
 * Class PhpEvaluator
 * @package Dompdf
 */
class PhpEvaluator
{
    /**
     * @var Canvas
     */
    private $canvas;

    /**
     * PhpEvaluator constructor.
     * @param Canvas $canvas
     */
    public function __construct(Canvas $canvas)
    {
        $this->canvas = $canvas;
    }

    /**
     * @param string $code
     * @param array $vars
     * @return void
     * @throws Exception
     */
    public function evaluate(string $code, array $vars = []): void
    {
        if (!$this->canvas->getDomPDF()->getOptions()->getIsPhpEnabled()) {
            return;
        }

        try {
            $this->executePHPCode($code, $vars);
        } catch (Exception $e) {
            throw new Exception('Error evaluating PHP code: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param Frame $frame
     * @return void
     * @throws Exception
     */
    public function render(Frame $frame): void
    {
        $this->evaluate($frame->getNode()->nodeValue);
    }

    /**
     * @param string $code
     * @param array $vars
     * @return void
     * @throws Exception
     */
    private function executePHPCode(string $code, array $vars = []): void
    {
        // Set up some variables for the inline code
        extract($vars);
        $pdf = $this->canvas;
        $fontMetrics = $pdf->getDomPDF()->getFontMetrics();
        $PAGE_NUM = $pdf
