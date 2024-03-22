<?php

declare(strict_types=1);

namespace Dompdf\Frame;

use Iterator;
use Dompdf\Frame;

/**
 * Class FrameTreeIterator
 *
 * Returns frames in preorder traversal order (parent then children).
 *
 * @access private
 * @package dompdf
 */
class FrameTreeIterator implements Iterator
{
    /**
     * @var Frame
     */
    protected $root;

    /**
     * @var Frame[]
     */
    protected array $stack = [];

    /**
     * @var int
     */
    protected int $num;

    /**
     * FrameTreeIterator constructor.
     *
     * @param Frame $root
     */
    public function __construct(Frame $root)
    {
        $this->root = $root;
        $this->stack[] = $root;
        $this->num = 0;
    }

    /**
     *
     */
    public function rewind(): void
    {
        $this->stack = [$this->root];
        $this->num = 0;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return count($this->stack) > 0;
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->num;
    }

    /**
     * @return Frame
     */
    public function current(): Frame
    {
        return end($this->stack);
    }

    /**
     * @return Frame
     */
    public function next(): Frame
    {
        $b = end($this->stack);

        // Pop last element
        unset($this->stack[key($this->stack)]);
        $this->num++;

        // Push all children onto the stack in reverse order
        if ($c = $b->getLastChild()) {
            $this->stack[] = $c;
            while ($c = $c->getPrevSibling()) {
                $this->stack[] = $c;
            }
        }

        return $b;
    }
}
