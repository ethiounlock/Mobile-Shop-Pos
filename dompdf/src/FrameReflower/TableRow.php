<?php

namespace Dompdf\FrameReflower;

use Dompdf\FrameDecorator\Block as BlockFrameDecorator;
use Dompdf\FrameDecorator\Table as TableFrameDecorator;
use Dompdf\FrameDecorator\TableRow as TableRowFrameDecorator;
use Dompdf\Exception;

/**
 * Reflows table rows
 *
 * @package dompdf
 */
class TableRow extends AbstractFrameReflower
{
    /**
     * @var TableRowFrameDecorator
     */
    private $frame;

    /**

