<?php

namespace unit\Cursor;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Paginator\Cursor\ArrayPages;
use ShoppingFeed\Paginator\Cursor\PageScrolledEvent;
use ShoppingFeed\Paginator\Cursor\Paginator;

class PaginatorTest extends TestCase
{
    private Paginator $paginator;

    public function setUp(): void
    {
        $this->paginator = new Paginator(
            new ArrayPages([
                ['item1', 'item2', 'item3'],
                ['item4', 'item5', 'item6'],
                ['item7', 'item8', 'item9'],
            ]),
        );
    }

    public function testPageIterator(): void
    {
        $this->assertCount(9, $this->paginator);
        $this->assertSame(
            ['item1', 'item2', 'item3', 'item4', 'item5', 'item6', 'item7', 'item8', 'item9'],
            iterator_to_array($this->paginator)
        );
    }
    public function testEventsAreDispatched(): void
    {
        $pages = [];

        $this->paginator
            ->getEventDispatcher()
            ->addListener(
                PageScrolledEvent::NAME,
                function (PageScrolledEvent $event) use (&$pages) {
                    $pages[] = $event->getPage();
                }
            );

        iterator_to_array($this->paginator);

        $this->assertSame([1, 2, 3], $pages);
    }
}
