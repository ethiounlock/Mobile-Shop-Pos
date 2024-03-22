<?php

namespace Sabberworm\CSS\Property;

/**
 * Class representing a single CSS selector.
 */
class Selector
{
    // Regexes for specificity calculations
    const NON_ID_ATTRIBUTES_AND_PSEUDO_CLASSES_RX = '/
        (?:\.[\w]+)               # classes
        |
        \[(\w+)\]                # attributes
        |
        :(                       # pseudo classes
            link|visited|active
            |hover|focus
            |lang|target
            |enabled|disabled|checked|indeterminate
            |root
            |nth-child|nth-last-child|nth-of-type|nth-last-of-type
            |first-child|last-child|first-of-type|last-of-type
            |only-child|only-of-type
            |empty|contains
        )
        /ix';

    const ELEMENTS_AND_PSEUDO_ELEMENTS_RX = '/
        (?:(^|[\s\+\>\~]+)[\w]+   # elements
        |
        :{1,2}(?:                # pseudo-elements
            after|before|first-letter|first-line|selection
        ))
        /ix';

    /**
     * @var string
     */
    private $selector;

    /**
     * @var int
     */
    private $specificity;

    /**
     * Selector constructor.
     * @param string $selector
     * @param bool $calculateSpecificity
     */
    public function __construct(string $selector, bool $calculateSpecificity = false)
    {
        $this->setSelector($selector);
        if ($calculateSpecificity) {
            $this->getSpecificity();
        }
    }

    /**
     * @return string
     */
    public function getSelector(): string
    {
        return $this->selector;
    }

    /**
     * @param string $selector
     */
    public function setSelector(string $selector): void
    {
        $this->selector = trim($selector);
        $this->specificity = null;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getSelector();
    }

    /**
     * @return int
