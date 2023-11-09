<?php

namespace unit\Cursor;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Event\Event;
use ShoppingFeed\Event\EventDispatcher;
use ShoppingFeed\Event\EventInterface;
use ShoppingFeed\Event\Test\EventDispatcherTracer;
use ShoppingFeed\Event\Test\EventTestCaseTrait;
use ShoppingFeed\Paginator\Cursor\ArrayPages;
use ShoppingFeed\Paginator\Cursor\CountablePageIterator;
use ShoppingFeed\Paginator\Cursor\Paginator;

class PaginatorTest extends TestCase
{
    use EventTestCaseTrait;

    private Paginator $paginator;

    public function setUp(): void
    {
        $this->paginator = new Paginator(
            new ArrayPages([
                ['item1', 'item2', 'item3'],
                ['item4', 'item5', 'item6'],
                ['item7', 'item8', 'item9'],
            ]),
            new EventDispatcher()
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
    public function testEventDispatcher(): void
    {
        $pages = [];

        $this->paginator
            ->getListenerRegistry()
            ->bind(
                Paginator::EVENT_PAGE_SCROLLED,
                function (EventInterface $event) use (&$pages) {
                    $pages[] = $event->getParam('page');
                }
            );

        iterator_to_array($this->paginator);

        $this->assertSame([1, 2, 3], $pages);
    }
}
