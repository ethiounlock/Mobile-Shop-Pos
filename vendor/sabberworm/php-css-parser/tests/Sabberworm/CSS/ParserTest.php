<?php

namespace Sabberworm\CSS;

use Sabberworm\CSS\CSSList\KeyFrame;
use Sabberworm\CSS\Value\Size;
use Sabberworm\CSS\Property\Selector;
use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\Property\AtRule;
use Sabberworm\CSS\Value\URL;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;

class ParserTest extends \PHPUnit\Framework\TestCase
{
    private $parser;

    protected function setUp()
    {
        $this->parser = new Parser();
    }

    public function testFiles()
    {
        $directory = dirname(__FILE__) . '/../../files';
        if ($handle = opendir($directory)) {
            while (false !== ($fileName = readdir($handle))) {
                if (strpos($fileName, '.') === 0) {
                    continue;
                }
                if (strrpos($fileName, '.css') !== strlen($fileName) - strlen('.css')) {
                    continue;
                }
                if (strpos($fileName, '-') === 0) {
                    continue;
                }
                $content = file_get_contents($directory . DIRECTORY_SEPARATOR . $fileName);
                try {
                    $document = $this->parser->parse($content);
                    $this->assertNotEquals('', $document->render());
                } catch (\Exception $e) {
                    $this->fail($e->getMessage());
                }
            }
            closedir($handle);
        }
    }

    // ... (other test methods)

    /**
     * Parse structure for file.
     *
     * @param string $sFileName Filename.
     *
     * @return CSSList\Document Parsed document.
     */
    private function parsedStructureForFile($sFileName)
    {
        $sFile = dirname(__FILE__) . '/../../files' . DIRECTORY_SEPARATOR . "$sFileName.css";
        $content = file_get_contents($sFile);
        return $this->parser->parse($content);
    }

    // ... (other test methods)
}
