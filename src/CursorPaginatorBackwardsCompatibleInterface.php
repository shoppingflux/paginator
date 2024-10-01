<?php

namespace ShoppingFeed\Paginator;

interface CursorPaginatorBackwardsCompatibleInterface extends CursorPaginatorInterface
{
    public function hasPreviousPage(): bool;
}
