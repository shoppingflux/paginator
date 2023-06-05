<?php

namespace ShoppingFeed\Paginator\Cursor;

use ShoppingFeed\Iterator\CountableTraversable;

class CountablePageIterator extends PageIterator implements CountableTraversable
{
    public function count(): int
    {
        return $this->first->getTotalCount();
    }
}
