<?php

namespace ShoppingFeed\Paginator;

interface PaginatorProviderInterface
{
    public function getCursor(): CursorPaginatorBackwardsCompatibleInterface;

    public function getPaginator(): PaginatorInterface;
}
