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
use Svg\Tag\Defs;
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

class Document extends AbstractTag
{
    /**
     * @var string|null
     */
    private $filename;

    /**
     * @var bool
     */
    private $inDefs = false;

    /**
     * @var int|null
     */
    private $x;

    /**
     * @var int|null
     */
    private $y;

    /**
     * @var int|null
     */
    private $width;

    /**
     * @var int|null
     */
    private $height;

    /**
     * @var AbstractTag[]
     */
    private $stack = [];

    /**
     * @var AbstractTag[]
     */
    private $defs = [];

    /**
     * @var Document[]
     */
    private $styleSheets = [];

    /**
     * @return SurfaceInterface
     */
    public function getSurface(): SurfaceInterface
    {
        // Implementation
    }

    /**
     * @return AbstractTag[]
     */
    public function getStack(): array
    {
        // Implementation
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        // Implementation
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        // Implementation
    }

    /**
     * @return array
     */
    public function getDimensions(): array
    {
        // Implementation
    }

    /**
     * @param string $attributes
     * @return array
     */
    private function handleSizeAttributes(string $attributes): array
    {
        // Implementation
    }

    /**
     * @return Document
     */
    public function getDocument(): self
    {
        // Implementation
    }

    /**
     * @param Document $stylesheet
     */
    public function appendStyleSheet(Document $stylesheet): void
    {
        // Implementation
    }

    /**
     * @return Document[]
     */
    public function getStyleSheets(): array
    {
        // Implementation
    }

    /**
     * @param array $attributes
     */
    protected function before(array $attributes): void
    {
        // Implementation
    }

    /**
     * @param SurfaceInterface $surface
     */
    public function render(SurfaceInterface $surface): void
    {
        // Implementation
    }

    /**
     * @param array $attributes
     */
    protected function svgOffset(array $attributes): void
    {
        // Implementation
    }

    /**
     * @param string $id
     * @return AbstractTag|null
     */
    public function getDef(string $id): ?AbstractTag
    {
        // Implementation
    }

    /**
     * @param string $data
     */
    private function _charData(string $data): void
    {
        // Implementation
    }

    /**
     * @param string $name
     * @param array $attributes
     */
    private function _tagStart(string $name, array $attributes): void
    {
        // Implementation
    }

    /**
     * @param string $name
     */
    private function _tagEnd(string $name): void
    {
        // Implementation
    }
}
