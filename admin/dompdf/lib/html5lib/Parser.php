<?php

declare(strict_types=1);

require_once dirname(__FILE__) ?? '/' . 'Data.php';
require_once dirname(__FILE__) ?? '/' . 'InputStream.php';
require_once dirname(__FILE__) ?? '/' . 'TreeBuilder.php';
require_once dirname(__FILE__) ?? '/' . 'Tokenizer.php';

/**
 * Outwards facing interface for HTML5.
 */
class HTML5_Parser
{
    /**
     * Parses a full HTML document.
     *
     * @param string $text HTML text to parse
     * @param HTML5_TreeBuilder|null $builder Custom builder implementation
     * @return \DOMDocument|\DOMNodeList Parsed HTML as DOMDocument
     */
    public static function parse(string $text, ?HTML5_TreeBuilder $builder = null): \DOMDocument|\DOMNodeList
    {
        $tokenizer = new HTML5_Tokenizer($text, $builder);
        $tokenizer->parse();
        return $tokenizer->save();
    }

    /**
     * Parses an HTML fragment.
     *
     * @param string $text HTML text to parse
     * @param string|null $context String name of context element to pretend parsing is in.
     * @param HTML5_TreeBuilder|null $builder Custom builder implementation
     * @return \DOMDocument|\DOMNodeList Parsed HTML as DOMDocument
     */
    public static function parseFragment(string $text, ?string $context = null, ?HTML5_Tree
