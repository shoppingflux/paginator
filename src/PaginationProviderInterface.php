<?php

namespace ShoppingFeed\Paginator;

interface PaginationProviderInterface
{
    public function getItemsPerPage(): int;

    public function getCurrentPage(): int;
}
