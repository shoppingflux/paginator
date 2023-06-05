<?php

namespace ShoppingFeed\Paginator\Cursor;

interface CountableCollectionInterface extends PageDiscoveryInterface
{
    /**
     * Get the total number of items for all remaining pages
     */
    public function getTotalCount(): int;
}
