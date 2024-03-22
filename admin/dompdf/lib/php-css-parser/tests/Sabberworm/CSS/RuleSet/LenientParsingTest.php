<?php

declare(strict_types=1);

namespace Sabberworm\CSS\RuleSet;

use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Settings;
use PHPUnit\Framework\TestCase;

class LenientParsingTest extends TestCase
{
    const FILE_PATH = __DIR__ . '/../../../files/';
    const FAULT_TOLERANCE_CSS = '-fault-tolerance.css';
    const END_TOKEN_CSS = '-end-token.css';
    const END_TOKEN_2_CSS = '-end-token-2.css';
    const CASE_INSENSITIVITY_CSS = 'case-insensitivity.css';

    /**
     * @expectedException \Sabberworm\CSS\Parsing\UnexpectedTokenException
     */
    public function testFaultToleranceOff()
    {
        $sFile = self::FILE_PATH . self::FAULT_TOLERANCE_CSS;
        $oParser = new Parser(file_get_contents($sFile), Settings::create()->beStrict());
        $oParser->parse();
    }

    /**
     * @test
     */
    public function testFaultToleranceOn()
    {
        $sFile = self::FILE_PATH . self::FAULT_TOLERANCE_CSS;
        $oParser = new Parser(file_get_contents($sFile), Settings::create()->withLenientParsing(true));
        $oResult = $oParser->parse();
        $this->assertSame('.test1 {}' . "\n" . '.test2 {hello: 2.2;hello: 2000000000000.2;}' . "\n" . '#test {}' . "\n" . '#test2 {help: none;}', $oResult->render());
    }

    /**
     * @expectedException \Sabberworm\CSS\Parsing\UnexpectedTokenException
     * @test
     */
    public function testEndToken()
    {
        $sFile = self::FILE_PATH . self::END_TOKEN_CSS;
        $oParser = new Parser(file_get_contents($sFile), Settings::create()->beStrict());
        $oParser->parse();
    }

    /**
     * @expectedException \Sabberworm\CSS\Parsing\UnexpectedTokenException
     * @test
     */
    public function testEndToken2()
    {
        $sFile = self::FILE_PATH . self::END_TOKEN_2_CSS;
        $oParser = new Parser(file_get_contents($sFile), Settings::create()->beStrict());
        $oParser->parse();
    }

    /**
     * @test
     */
    public function testEndTokenPositive()
    {
        $sFile = self::FILE_PATH . self::END_TOKEN_CSS;
        $oParser = new Parser(file_get_contents($sFile), Settings::create()->withLenientParsing(true));
        $oResult = $oParser->parse();
        $this->assertSame("", $oResult->render());
    }

    /**
     * @test
     */
    public function testEndToken2Positive()
    {
        $sFile = self::FILE_PATH . self::END_TOKEN_2_CSS;
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
        $sFile = self::FILE_PATH . self::FAULT_TOLERANCE_CSS;
        $oParser = new Parser(file_get_contents($sFile), Settings::create()->withLenientParsing(true));
        $oResult = $oParser->parse();
        $this->assertSame('.test1 {}' . "\n" . '.test2 {hello: 2.2;hello: 2000000000000.2;}' . "\n" . '#test {}' . "\n" . '#test2 {help: none;}', $oResult->render());
    }

    /**
     * @test
     */
    public function testCaseInsensitivity()
    {
        $sFile = self::FILE_PATH . self::CASE_INSENSITIVITY_CSS;
        $oParser = new Parser(file_get_contents($sFile));
        $oResult = $oParser->parse();
        $this->assertSame('@
