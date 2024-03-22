<?php

namespace Dompdf\Frame;

use Iterator;
use Countable;

/**
 * Linked-list Iterator
 *
 * Returns children in order and allows for list to change during iteration,
 * provided the changes occur to or after the current element
 *
 * @access private
 * @package dompdf
 */
class FrameListIterator implements Iterator, Countable
{

    /**
     * @var Frame
     */
    protected $_parent;

    /**
     * @var Frame
     */
    protected $_cur;

    /**
     * @var int
     */
    protected $_num;

    /**
     * @param Frame $frame
     */
    public function __construct(Frame $frame)
    {
        $this->_parent = $frame;
        $this->_cur = $frame->getFirstChild();
        $this->_num = 0;
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind(): void
    {
        $this->_cur = $this->_parent->getFirstChild();
        $this->_num = 0;
    }

    /**
     * Checks if the current position is valid
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->_cur !== null;
    }

    /**
     * Returns the key of the current element
     *
     * @return int
     */
    public function key(): int
    {
        return $this->_num;
    }

    /**
     * Returns the current Frame
     *
     * @return Frame|null
     */
    public function current(): ?Frame
    {
        return $this->_cur;
    }

    /**
     * Move forward to the next element
     *
     * @return Frame|null
     */
    public function next(): ?Frame
    {
        $ret = $this->_cur;
        if ($ret) {
            $
