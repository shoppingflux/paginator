<?php

namespace ShoppingFeed\Paginator\Adapter;

use Countable;
use ShoppingFeed\Iterator\IteratorInterface;

interface PaginatorAdapterInterface extends IteratorInterface, Countable
{
    public function limit($limit = null, $offset = null): void;

    /**
     * Return the total number of items without pagination applied
     */
    public function count(): int;
}
