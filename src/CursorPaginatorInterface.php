<?php

namespace ShoppingFeed\Paginator;

use Iterator;
use ShoppingFeed\Paginator\Cursor\PageDiscoveryInterface;

interface CursorPaginatorInterface extends PageDiscoveryInterface
{
    /**
     * Get the collection of elements limited by the pagination
     */
    public function getIterator(): Iterator;

    /**
     * Determine if there is a page to reach after
     */
    public function hasNextPage(): bool;

    /**
     * Get the collection of elements + their cursor limited by the pagination
     *
     * @return \Iterator|CursorEdge[]
     */
    public function getEdgeIterator(): Iterator;

    /**
     * Fetch the first cursor of the pagination set
     */
    public function getFirstCursor(): ?CursorInterface;

    /**
     * Fetch the first cursor of the pagination set
     */
    public function getLastCursor(): ?CursorInterface;

    /**
     * (Re) configure the paginator with the cursor specifications.
     *
     * @return self a new cursor instance with re-configured parts.
     */
    public function withCursor(CursorInterface $cursor): self;

    /**
     * Inform about the number of items that the current page handle
     */
    public function getItemsPerPage(): int;

    /**
     * The total number of elements that matched
     */
    public function getTotalCount(): int;
}
