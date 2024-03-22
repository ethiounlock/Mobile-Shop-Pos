<?php

namespace Sabberworm\CSS\CSSList;

use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\RuleSet\RuleSet;
use Sabberworm\CSS\Property\Selector;
use Sabberworm\CSS\Rule\Rule as CSSRule;
use Sabberworm\CSS\Value\ValueList;
use Sabberworm\CSS\Value\CSSFunction;

/**
 * A CSSBlockList is a CSSList whose DeclarationBlocks are guaranteed to contain valid declaration blocks or at-rules.
 * Most CSSLists conform to this category but some at-rules (such as @keyframes) do not.
 */
abstract class CSSBlockList extends CSSList
{
    private function __construct($iLineNo = 0)
    {
        parent::__construct($iLineNo);
    }

    /**
     * @return DeclarationBlock[]
     */
    protected function allDeclarationBlocks(): array
    {
        return $this->getValidContents(DeclarationBlock::class);
    }

    /**
     * @return RuleSet[]
     */
    protected function allRuleSets(): array
    {
        return $this->getValidContents(RuleSet::class);
    }

    /**
     * @param ValueList|CSSFunction $oElement
     * @param string $sSearchString
     * @param bool $bSearchInFunctionArguments
     * @return ValueList[]|CSSFunction[]|Selector[]|CSSRule[]
     */
    protected function allValues($oElement, ?string $sSearchString = null, bool $bSearchInFunctionArguments = false)
    {
        return $this->getValues($oElement, $sSearchString, $bSearchInFunctionArguments);
    }

    /**
     * @param string $sSpecificitySearch
     * @return Selector[]
     */
    protected function allSelectors(?string $sSpecificitySearch = null): array
    {
        $aDeclarationBlocks = $this->allDeclarationBlocks();
        $aSelectors = [];

        array_map(function (DeclarationBlock $oBlock) use (&$aSelectors, $sSpecificitySearch) {
            $oBlock->getSelectors()->map(function (Selector $oSelector) use (&$aSelectors, $sSpecificitySearch) {
                if ($sSpecificitySearch === null) {
                    $aSelectors[] = $oSelector;
                } else {
                    $sComparator = '===';
                    $aSpecificitySearch = explode(' ', $sSpecificitySearch);
                    $iTargetSpecificity = (int)$aSpecificitySearch[0];

                    if (count($aSpecificitySearch) > 1) {
                        $sComparator = $aSpecificitySearch[0];
                        $iTargetSpecificity = (int)$aSpecificitySearch[1];
                    }

                    $iSelectorSpecificity = $oSelector->getSpecificity();
                    $bMatches = false;

                    switch ($sComparator) {
                        case '<=':
                            $bMatches = $iSelectorSpecificity <= $iTargetSpecificity;
                            break;
                        case '<':
                            $bMatches = $iSelectorSpecificity < $iTargetSpecificity;
                            break;
                        case '>=':
                            $bMatches = $iSelectorSpecificity >= $iTargetSpecificity;
                            break;
                        case '>':
                            $bMatches = $iSelectorSpecificity > $iTargetSpecificity;
                            break;
                        default:
                            $bMatches = $iSelectorSpecificity === $iTargetSpecificity;
                            break;
                    }

                    if ($bMatches) {
                        $aSelectors[] = $oSelector;
                    }
                }
            });
        }, $aDeclarationBlocks);

        return $aSelectors;
    }

    /**
     * @param class-string<DeclarationBlock|RuleSet> $sClass
     * @return DeclarationBlock[]|RuleSet[]
     */
    private function getValidContents(string $sClass): array
    {
        return array_filter($this->aContents, function ($content) use ($sClass) {
            return $content instanceof $sClass;
        });
    }

    /**
     * @param ValueList|CSSFunction $oElement
     * @param string $sSearchString
     * @param bool $bSearchInFunctionArguments
     * @return ValueList[]|CSSFunction[]|Selector[]|CSSRule[]
     */
    private function getValues($oElement, ?string $sSearchString = null, bool $bSearchInFunctionArguments = false): array
    {
        $result = [];

        if ($oElement instanceof CSSBlockList) {
            array_map(function (ValueList|CSSFunction $content) use (&$result, $sSearchString, $bSearchInFunctionArguments) {
                $this->allValues($content, $result, $sSearchString, $bSearchInFunction
