<?php

namespace Sabberworm\CSS;

use PHPUnit\Framework\TestCase;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Rule\Rule;
use Sabberworm\CSS\Value\Size;

class DeclarationBlockTest extends TestCase
{
    /**
     * @dataProvider expandShorthandProvider
     * @param string $sCss
     * @param callable $sExpected
     */
    public function testExpandShorthandProperty(string $sCss, callable $sExpected): void
    {
        $oParser = new Parser($sCss);
        $oDoc = $oParser->parse();
        foreach ($oDoc->getAllDeclarationBlocks() as $oDeclaration) {
            $oDeclaration->expandShorthandProperty('border');
            $oDeclaration->expandShorthandProperty('background');
            $oDeclaration->expandShorthandProperty('margin');
            $oDeclaration->expandShorthandProperty('padding');
            $oDeclaration->expandShorthandProperty('font');
        }
        $this->assertEquals($sExpected(), (string) $oDoc);
    }

    public function expandShorthandProvider(): array
    {
        return [
            [
                'body{ border: 2px solid #000 }',
                function () {
                    return 'body {border-width: 2px;border-style: solid;border-color: #000;}';
                },
            ],
            // ... other test cases
        ];
    }

    /**
     * @dataProvider createShorthandProvider
     * @param string $sCss
     * @param callable $sExpected
     */
    public function testCreateShorthandProperty(string $sCss, callable $sExpected): void
    {
        $oParser = new Parser($sCss);
        $oDoc = $oParser->parse();
        foreach ($oDoc->getAllDeclarationBlocks() as $oDeclaration) {
            $oDeclaration->createShorthandProperty('border');
            $oDeclaration->createShorthandProperty('background');
            $oDeclaration->createShorthandProperty('margin');
            $oDeclaration->createShorthandProperty('padding');
            $oDeclaration->createShorthandProperty('font');
        }
        $this->assertEquals($sExpected(), (string) $oDoc);
    }

    public function createShorthandProvider(): array
    {
        return [
            [
                'body {border-width: 2px;border-style: solid;border-color: #000;}',
                function () {
                    return 'body {border: 2px solid #000;}';
                },
            ],
            // ... other test cases
        ];
    }

    public function testOverrideRules(): void
    {
        $sCss = '.wrapper { left: 10px; text-align: left; }';
        $oParser = new Parser($sCss);
        $oDoc = $oParser->parse();
        $oRule = new Rule('right');
        $oRule->setValue('-10px');
        $aContents = $oDoc->getContents();
        $oWrapper = $aContents[0];

        $this->assertCount(2, $oWrapper->getRules());
        $oWrapper->setRules(array($oRule));

        $aRules = $oWrapper->getRules();
        $this->assertCount(1, $aRules);
        $this->assertEquals('right', $aRules[0]->getRule());
        $this->assertEquals('-10px', $aRules[0]->getValue());
    }

    public function testRuleInsertion(): void
    {
        $sCss = '.wrapper { left: 10px; text-align: left; }';
        $oParser = new Parser($sCss);
        $oDoc = $oParser->parse();
        $aContents = $oDoc->getContents();
        $oWrapper = $aContents[0];

        $oFirst = $oWrapper->getRules('left');

