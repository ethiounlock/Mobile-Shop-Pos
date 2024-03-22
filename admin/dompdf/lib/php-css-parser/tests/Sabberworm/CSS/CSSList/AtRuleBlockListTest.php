<?php

namespace Sabberworm\CSS\CSSList;

use PHPUnit\Framework\TestCase;
use Sabberworm\CSS\Parser;

class AtRuleBlockListTest extends TestCase
{
    public function testMediaQueries()
    {
        $csses = [
            '@media(min-width: 768px){.class{color:red}}',
            '@media (min-width: 768px) {.class{color:red}}'
        ];

        foreach ($csses as $css) {
            $parser = new Parser($css);
            $doc = $parser->parse();
            $contents = $doc->getContents();
            $mediaQuery = $contents[0];

            $this->assertSame('media', $mediaQuery->atRuleName(), 'Does not interpret the type as a function');
            $this->assertSame('(min-width: 768px)', $mediaQuery->atRuleArgs(), 'The media query is the value');
        }
    }
}
