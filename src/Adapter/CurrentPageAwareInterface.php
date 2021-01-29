<?php
namespace ShoppingFeed\Paginator\Adapter;

use ShoppingFeed\Paginator\Value\AbsoluteInt;

interface CurrentPageAwareInterface
{
    /**
     * Let the adapter be aware of the paginator current page.
     * Some of implementations requires to uses the page instead of offset,
     * And this interface avoid the need to reverse page calculation from limit / offset values
     */
    public function setCurrentPage(AbsoluteInt $page): void;
}
