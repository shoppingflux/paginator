<?php

namespace ShoppingFeed\Paginator\Cursor;

use Traversable;

/**
 * This interface allow to get next page from the current page
 */
interface PageDiscoveryInterface extends Traversable
{
    /**
     * If any, fetch the next page
     */
    public function getNextPage(): ?self;
}
