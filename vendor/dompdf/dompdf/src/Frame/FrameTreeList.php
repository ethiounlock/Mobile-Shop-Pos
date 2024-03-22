<?php

namespace Dompdf\Frame;

use IteratorAggregate;
use Dompdf\Frame;

/**
 * Pre-order IteratorAggregate
 */
class FrameTreeList implements IteratorAggregate
{
    /**
     * @var Frame
     */
    protected $root;

    /**
     * FrameTreeList constructor.
     *
     * @param Frame $root
     */
    public function __construct(Frame $root)
    {
        $this->root = $root;
    }

    /**
     * @return FrameTreeIterator
     */
    public function getIterator(): FrameTreeIterator
    {
        return new FrameTreeIterator($this->root);
    }
}
