<?php
namespace ShoppingFeed\Paginator\Adapter;

use ShoppingFeed\Iterator\IteratorInterface;

interface PaginatorAdapterInterface extends IteratorInterface, \Countable
{
    /**
     * @param int $limit
     * @param int $offset
     *
     * @return $this
     */
    public function limit($limit = null, $offset = null);

    /**
     * Return the total number of items without pagination applied
     *
     * @return integer
     */
    public function count();
}
