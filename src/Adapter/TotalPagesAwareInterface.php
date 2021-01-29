<?php
namespace ShoppingFeed\Paginator\Adapter;

interface TotalPagesAwareInterface
{
    /**
     * Let the adapter be aware of the paginator total number of pages.
     * Some of implementations requires to uses the pages count,
     * and this interface avoid the need to reverse page calculation from limit / offset / total count.
     */
    public function setTotalPages(int $total): void;
}
