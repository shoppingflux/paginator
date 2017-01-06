<?php
namespace ShoppingFeed\Paginator;

interface PaginationProviderInterface
{
    /**
     * @return int
     */
    public function getItemsPerPage();

    /**
     * @return int
     */
    public function getCurrentPage();
}
