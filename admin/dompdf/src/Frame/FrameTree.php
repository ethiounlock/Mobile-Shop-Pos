<?php

namespace Dompdf\Frame;

use DOMDocument;
use DOMNode;
use DOMElement;
use DOMXPath;
use Dompdf\Exception;
use Dompdf\Frame;

/**
 * Represents an entire document as a tree of frames
 *
 * The FrameTree consists of {@link Frame} objects each tied to specific
 * DOMNode objects in a specific DomDocument.  The FrameTree has the same
 * structure as the DomDocument, but adds additional capabilities for
 * styling and layout.
 */
class FrameTree
{
    /**
     * Tags to ignore while parsing the tree
     *
     * @var array
     */
    protected static $hiddenTags = [
        "area",
        "base",
        "basefont",
        "head",
        "style",
        "meta",
        "title",
        "colgroup",
        "noembed",
        "param",
        "#comment"
    ];

    /**
     * The main DomDocument
     *
     * @var DOMDocument
     */
    protected $_dom;

    /**
     * The root node of the FrameTree.
     *
     * @var Frame
     */
    protected $_root;

    /**
     * Subtrees of absolutely positioned elements
     *
     * @var array of Frames
     */
    protected $_absoluteFrames;

    /**
     * A mapping of {@link Frame} objects to DOMNode objects
     *
     * @var array
     */
    protected $_registry;

    /**
     * Class constructor
     *
     * @param DOMDocument $dom the main DomDocument object representing the current html document
     */
    public function __construct(DOMDocument $dom)
    {
        $this->_dom = $dom;
        $this->_root = null;
        $this->_registry = [];
    }

    /**
     * Returns the DOMDocument object representing the current html document
     *
     * @return DOMDocument
     */
    public function getDom(): DOMDocument
    {
        return $this->_dom;

