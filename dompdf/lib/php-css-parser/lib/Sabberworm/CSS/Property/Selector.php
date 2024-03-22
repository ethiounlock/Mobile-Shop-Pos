<?php

namespace Sabberworm\CSS\Property;

/**
 * Class representing a single CSS selector.
 *
 * Selectors have to be split by the comma prior to being passed into this class.
 */
class Selector {

    //Regexes for specificity calculations
    const NON_ID_ATTRIBUTES_AND_PSEUDO_CLASSES_RX = '/
        (?:\.[\w-]+)|    # classes
        \[(?:\w+)[^\]]*]?|    # attributes
        (?:\:(?![#\da-z]+))?    # pseudo classes (excluding ID-like pseudo-classes)
        (?:
            (?:
                link|visited|active|hover|focus|lang|
                target|enabled|disabled|checked|indeterminate|
                root|nth-child|nth-last-child|nth-of-type|nth-last-of-type|
                first-child|last-child|first-of-type|last-of-type|
                only-child|only-of-type|
                empty|contains
            )
        )?
        /ix';

    const ELEMENTS_AND_PSEUDO_ELEMENTS_RX = '/
        (?:
            (?:^|[\s\+\>\~]+)[\w-]+    # elements
            |
            \:{1,2}(?:after|before|first-letter|first-line|selection)    # pseudo-elements
        )
        /ix';

    /**
     * @var string
     */
    private $sSelector;

    /**
     * @var int
     */
    private $iSpecificity;

    /**
     * Selector constructor.
     *
     * @param string $sSelector
     * @param bool   $bCalculateSpecificity
     */
    public function __construct(string $sSelector, bool $bCalculateSpecificity = false) {
        $this->validateSelector($sSelector);
        $this->sSelector = trim($sSelector);
        if ($bCalculateSpecificity) {
            $this->getSpecificity
