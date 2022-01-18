<?php

namespace ShoppingFeed\Paginator;

use Iterator;
use IteratorAggregate;

interface CursorPaginatorInterface extends IteratorAggregate
{
    /**
     * Get the collection of elements limited by the pagination
     */
    public function getIterator(): Iterator;

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
     * Determine if there is a page to reach after
     */
    public function hasNextPage(): bool;

    /**
     * If any, fetch the next page
     */
    public function getNextPage(): ?self;

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
