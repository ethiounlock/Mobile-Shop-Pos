<?php

declare(strict_types=1);

namespace Dompdf;

use Dompdf\Css\Style;
use Dompdf\Frame\FrameList;

/**
 * @package dompdf
 * @link    http://dompdf.github.com/
 * @author  Benj Carson <benjcarson@digitaljunkies.ca>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

/**
 * The main Frame class
 *
 * This class represents a single HTML element.  This class stores
 * positioning information as well as containing block location and
 * dimensions. Style information for the element is stored in a {@link
 * Style} object. Tree structure is maintained via the parent & children
 * links.
 *
 * @package dompdf
 */
class Frame
{
    const WS_TEXT = 1;
    const WS_SPACE = 2;

    /**
     * @var \DOMElement|\DOMText
     */
    protected $_node;

    /**
     * @var string
     */
    protected $_id;

    /**
     * @var Style
     */
    protected $_style;

    /**
     * @var Style
     */
    protected $_original_style;

    /**
     * @var Frame
     */
    protected $_parent;

    /**
     * @var Frame[]
     */
    protected $_frame_list;

    /**
     * @var Frame
     */
    protected $_first_child;

    /**
     * @var Frame
     */
    protected $_last_child;

    /**
     * @var Frame
     */
    protected $_prev_sibling;

    /**
     * @var Frame
     */
    protected $_next_sibling;

    /**
     * @var float[]
     */
    protected $_containing_block;

    /**
     * @var float[]
     */
    protected $_position;

    /**
     * @var float
     */
    protected $_opacity;

    /**
     * @var \Dompdf\FrameDecorator\AbstractFrameDecorator|null
     */
    protected $_decorator;

    /**
     * @var LineBox|null
     */
    protected $_containing_line;

    /**
     * @var bool
     */
    protected $_already_pushed = false;

    /**
     * @var bool
     */
    protected $_float_next_line = false;

    /**
     * @var bool
     */
    protected $_splitted;

    /**
     * @var int
     */
    protected static $_ws_state = self::WS_SPACE;

    /**
     * Class constructor
     *
     * @param \DOMNode $node the DOMNode this frame represents
     */
    public function __construct(\DOMNode $node)
    {
        $this->_node = $node;
        $this->_id = (string)self::$ID_COUNTER++;
        $this->_parent = null;
        $this->_first_child = null;
        $this->_last_child = null;
        $this->_prev_sibling = null;
        $this->_next_sibling = null;
        $this->_style = null;
        $this->_original_style = null;
        $this->_containing_block = [0, 0, 0, 0];
        $this->_position = [0, 0];
        $this->_opacity = 1.0;
        $this->_decorator = null;
        $this->_containing_line = null;
        $this->_is_cache = [];
        $this->_splitted = false;
    }

    // ... rest of the code ...
}
