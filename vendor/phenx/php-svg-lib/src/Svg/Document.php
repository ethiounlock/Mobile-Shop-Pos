<?php

declare(strict_types=1);

namespace Svg;

use Sabberworm\CSS\CSSList\Document;
use Sabberworm\CSS\CSSList\StyleDeclaration;
use Sabberworm\CSS\Parser;
use Svg\Surface\SurfaceInterface;
use Svg\Tag\AbstractTag;
use Svg\Tag\Anchor;
use Svg\Tag\Circle;
use Svg\Tag\ClipPath;
use Svg\Tag\Document as SvgDocument;
use Svg\Tag\Ellipse;
use Svg\Tag\Group;
use Svg\Tag\Image;
use Svg\Tag\Line;
use Svg\Tag\LinearGradient;
use Svg\Tag\Path;
use Svg\Tag\Polygon;
use Svg\Tag\Polyline;
use Svg\Tag\Rect;
use Svg\Tag\Stop;
use Svg\Tag\StyleTag;
use Svg\Tag\Text;
use Svg\Tag\UseTag;

/**
 * @package php-svg-lib
 * @link    http://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */
class Document extends AbstractTag
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var bool
     */
    protected $inDefs = false;

    /**
     * @var int|float
     */
    protected $x;

    /**
     * @var int|float
     */
    protected $y;

    /**
     * @var int|float
     */
    protected $width;

    /**
     * @var int|float
     */
    protected $height;

    /**
     * @var AbstractTag[]
     */
    protected $stack = [];

    /**
     * @var AbstractTag[]
     */
    protected $defs = [];

    /**
     * @var Document[]
     */
    protected $styleSheets = [];

    /**
     * @return SurfaceInterface
     */
    public function getSurface(): SurfaceInterface
    {
        // TODO: Implement getSurface() method.
    }

    /**
     * @return AbstractTag[]
     */
    public function getStack(): array
    {
        return $this->stack;
    }

    /**
     * @return int|float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int|float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return array
     */
    public function getDimensions(): array
    {
        $parser = new Parser();
        $css = $parser->parse($this->getStylesheetContent(), Parser::ONLY_CSS);

        $styleSheet = new Document();
        $styleSheet->addStylesheet($css);

        $rootAttributes = null;

        $parser = xml_parser_create("utf-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_element_handler(
            $parser,
            function ($parser, $name, $attributes) use (&$rootAttributes) {
                if ($name === "svg" && $rootAttributes === null) {
                    $attributes = array_change_key_case($attributes, CASE_LOWER);

                    $rootAttributes = $attributes;
                }
            },
            function ($parser, $name) {}
        );

        $fp = fopen($this->filename, "r");
        while ($line = fread($fp, 8192)) {
            xml_parse($parser, $line, false);

            if ($rootAttributes !== null) {
                break;
            }
        }

        xml_parser_free($parser);

        return $this->handleSizeAttributes($rootAttributes, $styleSheet);
    }

    /**
     * @param array $attributes
     * @param Document $styleSheet
     * @return array
     */
    protected function handleSizeAttributes(?array $attributes, Document $styleSheet): array
    {
        if ($this->width === null) {
            if (isset($attributes["width"])) {
                $width = Style::convertSize($attributes["width"], 400);
                $this->width  = $width;
            }

            if (isset($attributes["height"])) {
                $height = Style::convertSize($attributes["height"], 300);
                $this->height = $height;
            }

            if (isset($attributes['viewbox'])) {
                $viewBox = preg_split('/[\s,]+/is', trim($attributes['viewbox']));
                if (count($viewBox) == 4) {
                    $this->x = $viewBox[0];
                    $this->y = $viewBox[1];

                    if (!$this->width) {
                        $this->width = $viewBox[2];
                    }
                    if (!$this->height) {
                        $this->height = $viewBox[3];
                    }
                }
            }

            if (!$this->width || !$this->height) {
                $style = $styleSheet->getStyleForSelector('svg');
                if ($style instanceof StyleDeclaration) {
                    $this->width = $style->getPropertyValue('width');

