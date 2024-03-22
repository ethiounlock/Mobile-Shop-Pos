<?php

namespace Sabberworm\CSS\Value;

/**
 * Abstract class representing a list of CSS values.
 */
abstract class ValueList extends Value
{
    /**
     * @var Value[]
     */
    protected array $aComponents;

    /**
     * @var string
     */
    protected string $sSeparator;

    /**
     * ValueList constructor.
     * @param Value[] $aComponents
     * @param string $sSeparator
     * @param int|null $iLineNo
     */
    public function __construct(array $aComponents = [], string $sSeparator = ',', int $iLineNo = null)
    {
        parent::__construct($iLineNo);
        $this->aComponents = $aComponents;
        $this->sSeparator = $sSeparator;
    }

    /**
     * Add a component to the list.
     * @param Value $mComponent
     */
    public function addListComponent(Value $mComponent): void
    {
        parent::add($mComponent);
        $this->aComponents[] = $mComponent;
    }

    /**
     * Get the list components.
     * @return Value[]
     */
    public function getListComponents(): array
    {
        return $this->aComponents;
    }

    /**
     * Set the list components.
     * @param Value[] $aComponents
     */
    public function setListComponents(array $aComponents): void
    {
        $this->aComponents = $aComponents;
    }

    /**
     * Get the list separator.
     * @return string
     */
