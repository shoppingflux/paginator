<?php
namespace ShoppingFeed\Paginator;

use ShoppingFeed\Iterator\FilterAggregateIteratorInterface;
use ShoppingFeed\Iterator\IteratorInterface;
use ShoppingFeed\Paginator\Adapter\PaginatorAdapterInterface;

interface PaginatorInterface extends
    PaginationProviderInterface,
    IteratorInterface,
    FilterAggregateIteratorInterface,
    \Countable
{
    /**
     * Define the current page
     *
     * @param int $number greater than 0
     *
     * @return $this
     */
    public function setCurrentPage($number): self;

    /**
     * Define the number of items per page
     *
     * @param int $number greater than 0
     *
     * @return $this
     */
    public function setItemsPerPage($number): self;

    /**
     * Return the total count of items without applying pagination
     */
    public function getTotalCount(): int;

    /**
     * @return integer|null Null if the current page is the last one
     */
    public function getNextPage(): ?int;

    /**
     * @return integer|null Null if the current page is the first one
     */
    public function getPrevPage(): ?int;

    /**
     * @return int The number of pages
     */
    public function getTotalPages(): int;
}
