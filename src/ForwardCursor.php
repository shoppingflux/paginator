<?php

namespace ShoppingFeed\Paginator;

class ForwardCursor extends CursorSerializable
{
    public function __construct(int $limit, string $value = '')
    {
        $this->setValue($value);
        $this->setLimit($limit);
        $this->setDirection(self::PAGE_NEXT);
    }
}
