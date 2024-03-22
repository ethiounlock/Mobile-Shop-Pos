<?php

namespace Svg;

use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\Property\Selector;
use Sabberworm\CSS\Rule\Rule;

/**
 * Class Style
 * @package Svg
 */
class Style
{
    public const TYPE_COLOR = 1;
    public const TYPE_LENGTH = 2;
    public const TYPE_NAME = 3;
    public const TYPE_ANGLE = 4;
    public const TYPE_NUMBER = 5;

    /**
     * @var array
     */
    protected $styleMap = [
        'color' => ['color', self::TYPE_COLOR],
        'opacity' => ['opacity', self::TYPE_NUMBER],
        'display' => ['display', self::TYPE_NAME],

        'fill' => ['fill', self::TYPE_COLOR],
        'fill-opacity' => ['fillOpacity', self::TYPE_NUMBER],
        'fill-rule' => ['fillRule', self::TYPE_NAME],

        'stroke' => ['stroke', self::TYPE_COLOR],
        'stroke-dasharray' => ['strokeDasharray', self::TYPE_NAME],
        'stroke-dashoffset' => ['strokeDashoffset', self::TYPE_NUMBER],
        'stroke-linecap' => ['strokeLinecap', self::TYPE_NAME],
        'stroke-linejoin' => ['strokeLinejoin', self::TYPE_NAME],
        'stroke-miterlimit' => ['strokeMiterlimit', self::TYPE_NUMBER],
        'stroke-opacity' => ['strokeOpacity', self::TYPE_NUMBER],
        'stroke-width' => ['strokeWidth', self::TYPE_NUMBER],

        'font-family' => ['fontFamily', self::TYPE_NAME],
        'font-size' => ['fontSize', self::TYPE_NUMBER],
        'font-weight' => ['fontWeight', self::TYPE_NAME],
        'font-style' => ['fontStyle', self::TYPE_NAME],
        'text-anchor' => ['textAnchor', self::TYPE_NAME],
    ];

    /**
     * @var string
     */
    public $color;

    /**
     * @var float
     */
    public $opacity;

    /**
     * @var string
     */
    public $display;

    /**
     * @var string
     */
    public $fill;

    /**
     * @var float
     */
    public $fillOpacity;

    /**
     * @var string
     */
    public $fillRule;

    /**
     * @var string
     */
    public $stroke;

    /**
     * @var float
     */
    public $strokeOpacity;

    /**
     * @var string
     */
    public $strokeLinecap;

    /**
     * @var string
     */
    public $strokeLinejoin;

    /**
     * @var float
     */
    public $strokeMiterlimit;

    /**
     * @var float
     */
    public $strokeWidth;

    /**
     * @var string
     */
    public $strokeDasharray;

    /**
     * @var float
     */
    public $strokeDashoffset;

    /**
     * @var string
     */
    public $fontFamily = 'serif';

    /**
     * @var float
     */
    public $fontSize = 12;

    /**
     * @var string
     */
    public $fontWeight = 'normal';

    /**
     * @var string
     */
    public $fontStyle = 'normal';

    /**
     * @var string
     */
    public $textAnchor = 'start';

    /**
     * Style constructor.
     */
    public function __construct()
    {
        $this->color = 'black';
        $this->opacity = 1;
        $this->display = 'inline';

        $this->fill = 'none';
        $this->fillOpacity = 1;
        $this->fillRule = 'nonzero';

        $this->stroke = 'none';
        $this->strokeOpacity = 1;
        $this->strokeLinecap = 'butt';
        $this->strokeLinejoin = 'miter';
        $this->strokeMiterlimit = 4;
        $this->strokeWidth = 1;
        $this->strokeDasharray = 'none';
        $this->strokeDashoffset = 0;

        $this->fontFamily = 'serif';
        $this->fontSize = 12;
        $this->fontWeight = 'normal';
        $this->fontStyle = 'normal';
        $this->textAnchor = 'start';
    }

    /**
     * @param array $attributes
     *
     * @return Style
     */
    public function fromAttributes(array $attributes): self
    {
        $this->fillStyles($attributes);

        if (isset($attributes["style"])) {
            $styles = self::parseCssStyle($attributes["style"]);
            $this->fillStyles($styles);
        }

        return $this;
    }

    /**
     * @param AbstractTag $tag
     */
    public function inherit(AbstractTag $tag): void
