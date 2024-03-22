<?php

namespace Sabberworm\CSS\Property;

use Sabberworm\CSS\Renderable;
use Sabberworm\CSS\Comment\Commentable;
use Sabberworm\CSS\Exception\InvalidArgumentException;

interface AtRule extends Renderable, Commentable
{
    // Since there are more set rules than block rules, we’re whitelisting the block rules and have anything else be treated as a set rule.
    const BLOCK_RULES = 'media, document, supplies, region-style, font-feature-values';
    // …and more font-specific ones (to be used inside font-feature-values)
    const SET_RULES = 'font-face, counter-style, page, swash, styleset, annotation';

    /**
     * @return string
     */
    public function atRuleName();

    /**
     * @return string
     */

