<?php

namespace Sabberworm\CSS\Property;

use Sabberworm\CSS\Renderable;
use Sabberworm\CSS\Comment\Commentable;
use Sabberworm\CSS\Exception\InvalidAtRuleException;

interface AtRule extends Renderable, Commentable {
    // Since there are more set rules than block rules, we’re whitelisting the block rules and have anything else be treated as a set rule.
    const BLOCK_RULES = 'media, document, supplies, region-style, font-feature-values';
    // …and more font-specific ones (to be used inside font-feature-values)
    const SET_RULES = 'font-face, counter-style, page, swash, styleset, annotation';

    public function atRuleName(): string;

    public function atRuleArgs(): array;

    public static function isBlockRule(string $ruleName): bool;

    public static function isSetRule(string $ruleName): bool;
}

// Exception class for invalid AtRule
class InvalidAtRuleException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
