<?php

namespace ShoppingFeed\Paginator;

use PHPUnit\Framework\TestCase;

class PageIteratorTest extends TestCase
{
    public function testPageIterator(): void
    {
        $iterator = new PageIterator(
            new ArrayPages([
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
    public function testCountablePageIterator(): void
    {
        $iterator = new CountablePageIterator(
            new ArrayPages([
                ['item1', 'item2', 'item3'],
                ['item4', 'item5', 'item6'],
                ['item7', 'item8', 'item9'],
            ])
        );

        $this->assertSame(
            ['item1', 'item2', 'item3', 'item4', 'item5', 'item6', 'item7', 'item8', 'item9'],
            iterator_to_array($iterator)
        );
        $this->assertCount(9, $iterator);
    }
}
