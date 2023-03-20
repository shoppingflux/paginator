<?php

namespace ShoppingFeed\Paginator;

class ForwardCursor extends CursorSerializable
{
    public function __construct(int $limit)
    {
        $this->setValue('');
        $this->setLimit($limit);
        $this->setDirection(self::PAGE_NEXT);
    }

    public function withValue(string $value): self
    {
        $cursor = clone $this;
        $cursor->setValue($value);

        return $cursor;
    }
}
