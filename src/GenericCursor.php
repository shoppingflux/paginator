<?php

namespace ShoppingFeed\Paginator;

class GenericCursor extends CursorSerializable
{
    public function __construct(string $value, int $limit, string $direction = self::PAGE_NEXT)
    {
        $this->setValue($value);
        $this->setLimit($limit);
        $this->setDirection($direction);
    }
}
