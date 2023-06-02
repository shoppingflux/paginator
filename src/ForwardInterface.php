<?php

namespace ShoppingFeed\Paginator;

use Traversable;

/**
 * This interface allow to get next page from a current page.
 */
interface ForwardInterface extends Traversable
{
    /**
     * Determine if there is a page to reach after
     */
    public function hasNextPage(): bool;

    /**
     * If any, fetch the next page
     */
    public function getNextPage(): ?self;
}
