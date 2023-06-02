<?php

namespace ShoppingFeed\Paginator;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Paginator\ArrayPaginator;

class CursorPaginatorIteratorTest extends TestCase
{
    public function testIterator(): void
    {
        $iterator = new CursorPaginatorIterator(
            new ArrayPaginator([
                ['item1', 'item2', 'item3'],
                ['item4', 'item5', 'item6'],
                ['item7', 'item8', 'item9'],
            ])
        );

        $this->assertSame(
            ['item1', 'item2', 'item3', 'item4', 'item5', 'item6', 'item7', 'item8', 'item9'],
            iterator_to_array($iterator)
        );
    }
}
