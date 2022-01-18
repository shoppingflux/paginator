<?php

namespace ShoppingFeed\Paginator\Value;

use ShoppingFeed\Paginator\Exception;

class AbsoluteInt
{
    /**
     * @var int
     */
    private $value;

    public function __construct($value)
    {
        if (is_object($value) && is_callable([$value, '__toString'])) {
            $value = (string) $value;
        }

        if (! is_numeric($value)) {
            throw new Exception\InvalidArgumentException('Expecting an numeric value');
        }

        $value = (int) $value;
        if ($value < 0) {
            throw new Exception\DomainException('Expecting an numeric value greater than equal of 0');
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * @return int
     */
    public function toInt()
    {
        return $this->value;
    }
}
