#!/usr/bin/env php
<?php

declare(strict_types=1);

use Sabberworm\CSS\CSSList\ListRuleSet;
use Sabberworm\CSS\Parser;

function parseCss($input) : ListRuleSet
{
    if (!file_exists($input)) {
        throw new \Exception("File not found: $input");
    }

    require_once dirname(__FILE__) . '/bootstrap.php';

    $source = file_get_contents($input);
    if ($source === false) {
        throw new \Exception("Error reading file: $input");
    }

    $parser = new Parser($source);
    $document = $parser->parse();

    return $document;
}

function printDocument(ListRuleSet $document) : void
{
    echo "\n" . '#### Input' . "\n\n```css\n";
    echo highlight_string($document->getOriginalCode(), true);

    echo "\n```\n\n" . '#### Structure (`var_dump()`)' . "\n\n```php\n";
    echo '<pre>';
    var_dump($document);
    echo '</pre>';

    echo "\n```\n\n" . '#### Output (`render()`)' . "\n\n```css\n";
    echo highlight_string($document->render(), true);
    echo "\n```\n";
}

try {
    $inputFile = 'php://stdin';
    $document = parseCss($inputFile);
    printDocument
