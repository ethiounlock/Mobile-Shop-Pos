<?php

namespace Sabberworm\CSS\Value;

/**
 * Abstract class representing a primitive CSS value.
 */
abstract class PrimitiveValue extends Value
{
    /**
     * PrimitiveValue constructor.
     * @param int $iLineNo
     */
    final public function __construct(int $iLineNo = 0)
    {
        parent::__construct($iLineNo);
    }
}

/**
 * Concrete example of a PrimitiveValue subclass.
 */
class LengthValue extends PrimitiveValue
{
    // Implementation details of the LengthValue class.
}
