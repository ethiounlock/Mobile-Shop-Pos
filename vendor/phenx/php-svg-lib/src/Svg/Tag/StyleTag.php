<?php

namespace Svg\Tag;

use Sabberworm\CSS;

/**
 * Class StyleTag
 * @package Svg\Tag
 *
 * @author Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */
class StyleTag extends AbstractTag
{
    /**
     * @var string
     */
    protected $text = '';

    /**
     * @param string $text
     */
    public function appendText(string $text): void
    {
        $this->text .= $text;
    }

    /**
     * @inheritdoc
     */
    public function end(): void
    {
        if (!empty($this->text)) {
            $parser = new CSS\Parser($this->text);
            $this->document->appendStyleSheet($parser->parse());
        }
    }
}
