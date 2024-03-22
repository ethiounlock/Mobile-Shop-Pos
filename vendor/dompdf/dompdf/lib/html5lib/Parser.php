<?php

declare(strict_types=1);

require_once dirname(__FILE__) . '/Data.php';
require_once dirname(__FILE__) . '/InputStream.php';
require_once dirname(__FILE__) . '/TreeBuilder.php';
require_once dirname(__FILE__) . '/Tokenizer.php';

/**
 * Outwards facing interface for HTML5.
 */
class HTML5_Parser
{
    /**
     * Parses a full HTML document.
     *
     * @param string                                 $text      HTML text to parse
     * @param null|HTML5_Tokenizer&HTML5_BuilderInterface $builder Custom builder implementation
     *
     * @return DOMDocument Parsed HTML as DOMDocument
     */
    public static function parse(string $text, ?HTML5_Tokenizer &$builder = null): DOMDocument
    {
        $tokenizer = new HTML5_Tokenizer($text, $builder);
        $tokenizer->parse();

        $dom = $tokenizer->save();

        if ($dom === false) {
            throw new RuntimeException('Failed to save parsed HTML as DOMDocument.');
        }

        return $dom;
    }

    /**
     * Parses an HTML fragment.
     *
     * @param string                                 $text      HTML text to parse
     * @param string|null                              $context String name of context element to pretend parsing is in.
     * @param null|HTML5_Tokenizer&HTML5_BuilderInterface $builder Custom builder implementation
     *
     * @return DOMNode Parsed HTML as DOMNode
     */
    public static function parseFragment(string $text, ?string $context = null, ?HTML5_Tokenizer &$builder = null): DOMNode
    {
        $tokenizer = new HTML5_Tokenizer($text, $builder);

