<?php

namespace ShoppingFeed\Paginator\Cursor;

use Symfony\Contracts\EventDispatcher\Event;

class PageScrolledEvent extends Event
{
    public const NAME = 'paginator.page.scrolled';

    private int $page;

    public function __construct(int $page)
    {
        $this->page = $page;
    }

    public function getPage(): int
    {
        return $this->page;
    }
}
