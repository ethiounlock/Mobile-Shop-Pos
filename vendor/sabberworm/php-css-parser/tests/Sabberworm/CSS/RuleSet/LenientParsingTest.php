<?php

namespace Sabberworm\CSS\RuleSet;

use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Settings;

class LenientParsingTest
{
    const FILE_PATH = __DIR__ . '/../../../files/';

    /**
     * @test
     * @expectedException Sabberworm\CSS\Parsing\UnexpectedTokenException
     */
    public function testFaultToleranceOff()
    {
        $sFile = self::FILE_PATH . "-fault-tolerance.css";
        $oParser = new Parser(file_get_contents($sFile), Settings::create()->beStrict());
        $oParser->parse();
    }

    /**
     * @test
     */
    public function testFaultToleranceOn()
    {
        $sFile = self::FILE_PATH . "-fault-tolerance.css";
        $oParser = new Parser(file_get_contents($sFile), Settings::create()->withLenientParsing(true));
        $oResult = $oParser->parse();

        $this->assertSame(
            '.test1 {}' . PHP_EOL
            . '.test2 {hello: 2.2;hello: 2000000000000.2;}' . PHP_EOL
            . '#test {}' . PHP_EOL
            . '#test2 {help: none;}',
            $oResult->render()
        );
    }

    /**
     * @test
     * @expectedException Sabberworm\CSS\Parsing\UnexpectedTokenException
     */
    public function testEndToken()
    {
        $sFile = self::FILE_PATH . "-end-token.css";
        $oParser = new Parser(file_get_contents($sFile), Settings::create()->beStrict());
        $oParser->parse();
    }

    /**
     * @test
     * @expectedException Sabberworm\CSS\Parsing\UnexpectedTokenException
     */
    public function testEndToken2()
    {
        $sFile = self::FILE_PATH . "-end-token-2.css";
        $oParser = new Parser(file_get_contents($sFile), Settings::create()->beStrict());
        $oParser->parse();
    }

    /**
     * @test
     */
    public function testEndTokenPositive()
    {
        $sFile = self::FILE_PATH . "-end-token.css";
        $oParser = new Parser(file_get_contents($sFile), Settings::create()->withLenientParsing(true));
        $oResult = $oParser->parse();
        $this->assertSame("", $oResult->render());
    }

    /**
     * @test
     */
    public function testEndToken2Positive()
    {
        $sFile = self::FILE_PATH . "-end-token-2.css";
        $oParser = new Parser(file_get_contents($sFile), Settings::create()->withLenientParsing(true));
        $oResult = $oParser->parse();
        $this->assertSame('#home .bg-layout {background-image: url("/bundles/main/img/bg1.png?5");}', $oResult->render());
    }

    /**
     * @test
     */
    public function testLocaleTrap()
    {
        setlocale(LC_ALL, "pt_PT", "no");
        $sFile = self::FILE_PATH . "-fault-tolerance.css";
        $oParser = new Parser(file_get_contents($sFile), Settings::create()->withLenientParsing(true));
        $oResult = $oParser->parse();
        $this->assertSame(
            '.test1 {}' . PHP_EOL
            . '.test2 {hello: 2.2;hello: 2000000000000.2;}' . PHP_EOL
            . '#test {}' . PHP_EOL
            . '#test2 {help: none;}',
            $oResult->render()
        );
    }

    /**
     * @test
     */
    public function testCaseInsensitivity()
    {
        $sFile = self::FILE_PATH . "case-insensitivity.css";
        $oParser = new Parser(file_get_contents($sFile));
        $oResult = $oParser->parse();

        $this->assertSame(
            '@charset "utf-8";' . PHP_EOL
            . '@import url("test.css");' . PHP_EOL
            . '@media screen {}' . PHP_EOL
            . '#myid {case: insensitive !important;frequency: 30Hz;font-size: 1em;color: #ff
