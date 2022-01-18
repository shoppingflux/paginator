<?php

namespace ShoppingFeed\Paginator;

interface PaginatorProviderInterface
{
    public function getCursor(): CursorPaginatorInterface;

    public function getPaginator(): PaginatorInterface;
}
