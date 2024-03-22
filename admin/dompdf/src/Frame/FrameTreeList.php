<?php

namespace Dompdf\Frame;

use IteratorAggregate;
use Dompdf\Frame;

/**
 * Pre-order IteratorAggregate
 *
 * This class provides a pre-order iterator for traversing a tree of Frame objects.
 */
class FrameTreeList implements IteratorAggregate
{
    /**
     * @var Frame|null
     */
    protected $root;

    /**
     * FrameTreeList constructor.
     *
     * @param Frame $root The root Frame object of the tree.
     */
    public function __construct(Frame $root = null)
    {
        $this->root = $root;
    }

    /**
     * Returns an iterator for traversing the Frame tree in pre-order.
     *
     * @return FrameTreeIterator
     */
    public function getIterator()
    {
        return new FrameTreeIterator($this->root);
    }
}
