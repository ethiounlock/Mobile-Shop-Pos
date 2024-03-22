<?php

namespace Sabberworm\CSS\CSSList;

use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Document;

/**
 * Test for Document class
 */
class DocumentTest extends \PHPUnit_Framework_TestCase {

    /**
     * Test overriding contents of a CSS document
     */
    public function testOverrideContents() {
        $sCss = '.thing { left: 10px; }';
        $oParser = new Parser($sCss);
        $oDoc = $oParser->parse();

        // Get the initial contents of the document
        $initialContents = $oDoc->getContents();
        $this->assertCount(1, $initialContents);

        $sCss2 = '.otherthing { right: 10px; }';
        $oParser2 = new Parser($sCss2);
        $oDoc2 = $oParser2->parse();

        // Override the contents of the document
        $oDoc->setContents(array_merge($initialContents, $oDoc2->getContents()));
        $finalContents = $oDoc->getContents();

        // Check if the contents have been overridden correctly
        $this->assertCount(2, $finalContents);
        $this->assertEquals('.thing { left: 10px; }', $finalContents[0]);
        $this->assertEquals('.otherthing { right: 10px; }', $finalContents[1]);
    }

}
