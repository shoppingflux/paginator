<?php

namespace ShoppingFeed\Paginator\Cursor;

use IteratorAggregate;

/**
 * This interface allow to get next page from the current page
 */
interface PageDiscoveryInterface extends IteratorAggregate
{
    /**
     * If any, fetch the next page
     */
    public function getNextPage(): ?self;

    /**
     * Get the total number of items for all remaining pages including current
     * pages items
     */
    public function getTotalCount(): int;
}
