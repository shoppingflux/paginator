<?php
namespace ShoppingFeed\Paginator;

interface CursorPaginatorInterface extends \IteratorAggregate
{
    /**
     * Get the collection of elements limited by the pagination
     */
    public function getIterator(): \Iterator;

    /**
     * Fetch the first cursor of the pagination set
     */
    public function getFirstCursor(): ?string;

    /**
     * Fetch the first cursor of the pagination set
     */
    public function getLastCursor(): ?string;

    /**
     * Determine if there is a page to reach after
     */
    public function hasNextPage(): bool;

    /**
     * If any, fetch the next page
     */
    public function getNextPage(): ?self;

    /**
     * (Re) configure the paginator with the following information:
     * - limit (int, required)     : Max elements to fetch for that cursor
     * - after (string, optional)  : Cursor from where to fetch elements
     * - before (string, optional) : Cursor until where to fetch elements. Ignored if after is present.
     *
     * @return self a new cursor instance with re-configured parts.
     */
    public function configure(array $cursor): self;

    /**
     * Inform about the number of items that the current page handle
     */
    public function getItemsPerPage(): int;

    /**
     * The total number of elements that matched
     */
    public function getTotalCount(): int;
}
