<?php

namespace Dompdf;

/**
 * Interface for rendering PDFs.
 *
 * Implementations should measure x and y increasing to the left and down,
 * respectively, with the origin in the top left corner.  Implementations
 * are free to use a unit other than points for length, but I can't
 * guarantee that the results will look any good.
 */
interface Canvas
{
    /**
     * Canvas constructor.
     *
     * @param string $paper The paper size.
     * @param string $orientation The page orientation.
     */
    public function __construct(string $paper = "letter", string $orientation = "portrait");

    /**
     * Returns the Dompdf instance associated with this canvas.
     *
     * @return Dompdf
     */
    public function getDompdf(): Dompdf;

    /**
     * Returns the current page number.
     *
     * @return int
     */
    public function getPageNumber(): int;

    /**
     * Returns the total number of pages.
     *
     * @return int
     */
    public function getPageCount(): int;

    /**
     * Sets the total number of pages.
     *
     * @param int $count The total number of pages.
     */
    public function setPageCount(int $count): void;

    /**
     * Draws a line from x1,y1 to x2,y2.
     *
     * @param float $x1 The x-coordinate of the start point.
     * @param float $y1 The y-coordinate of the start point.
     * @param float $x2 The x-coordinate of the end point.
     * @param float $y2 The y-coordinate of the end point.
     * @param array $color The line color.
     * @param float $width The line width.
     * @param array $style The line style (dash pattern).
     */
    public function line(
        float $x1,
        float $y1,
        float $x2,
        float $y2,
        array $color,
        float $width,
        ?array $style = null
    ): void;

    /**
     * Draws a rectangle at x1,y1 with width w and height h.
     *
     * @param float $x1 The x-coordinate of the top-left corner.
     * @param float $y1 The y-coordinate of the top-left corner.
     * @param float $w The width of the rectangle.
    
