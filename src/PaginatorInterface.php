<?php
namespace ShoppingFeed\Paginator;

use ShoppingFeed\Iterator\IteratorInterface;

interface PaginatorInterface extends IteratorInterface, \Countable
{
    /**
     * Define the current page
     *
     * @param int $number greater than 0
     *
     * @return $this
     */
    public function setCurrentPage($number);

    /**
     * Define the number of items per page
     *
     * @param int $number greater than 0
     *
     * @return $this
     */
    public function setItemsPerPage($number);

    /**
     * Return the total count of items without applying pagination
     *
     * @return integer
     */
    public function getTotalCount();

    /**
     * Return the current page number
     *
     * @return integer
     */
    public function getCurrentPage();

    /**
     * Return the number of items per page
     *
     * @return integer
     */
    public function getItemsPerPage();

    /**
     * @return integer|null Null if the current page is the last one
     */
    public function getNextPage();

    /**
     * @return integer|null Null if the current page is the first one
     */
    public function getPrevPage();
}
