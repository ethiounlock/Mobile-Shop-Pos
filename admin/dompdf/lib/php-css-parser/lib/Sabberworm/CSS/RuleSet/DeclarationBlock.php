<?php

declare(strict_types=1);

namespace Sabberworm\CSS\RuleSet;

use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\OutputException;
use Sabberworm\CSS\Property\Selector;
use Sabberworm\CSS\Rule\Rule;
use Sabberworm\CSS\Value\RuleValueList;
use Sabberworm\CSS\Value\Value;
use Sabberworm\CSS\Value\Size;
use Sabberworm\CSS\Value\Color;
use Sabberworm\CSS\Value\URL;

/**
 * Declaration blocks are the parts of a CSS file which denote the rules belonging to a selector.
 * Declaration blocks usually appear directly inside a Document or another CSSList (mostly a MediaQuery).
 */
class DeclarationBlock extends RuleSet
{
    /** @var Selector[] */
    private $aSelectors;

    public function __construct(int $iLineNo = 
