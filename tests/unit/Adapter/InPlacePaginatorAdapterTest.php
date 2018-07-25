<?php
namespace ShoppingFeed\Paginator\Adapter;

use PHPUnit\Framework\TestCase;

class InPlacePaginatorAdapterTest extends TestCase
{
    public function testAcceptArrayAsIterable()
    {
        $instance = new InPlacePaginatorAdapter(['foo', 'bar']);

        $this->assertSame(['foo', 'bar'], $instance->toArray());
    }

    public function testAcceptIteratorAggregateAsIterable()
    {
        $instance = new InPlacePaginatorAdapter(
            new class implements \IteratorAggregate
            {
                public function getIterator()
                {
                    return new \ArrayIterator(['foo', 'bar']);
                }
            }
        );

        $this->assertSame(['foo', 'bar'], $instance->toArray());
    }

    public function testWithNoLimitTheIteratorCompletesItsIteration()
    {
        $instance = new InPlacePaginatorAdapter(new \ArrayIterator(['foo', 'bar']));

        $this->assertSame(['foo', 'bar'], $instance->toArray());
    }

    public function testPaginatorSkipsResultsBeforeOffset()
    {
        $instance = new InPlacePaginatorAdapter(new \ArrayIterator(['foo', 'bar', 'baz']));

        $instance->limit(null, 1);
        $this->assertSame([1 => 'bar', 2 => 'baz'], $instance->toArray());

        $instance->limit(null, 2);
        $this->assertSame([2 => 'baz'], $instance->toArray());
    }

    public function testPaginatorStopsAfterLimit()
    {
        $instance = new InPlacePaginatorAdapter(new \ArrayIterator(['foo', 'bar', 'baz']));

        $instance->limit(1);
        $this->assertSame(['foo'], $instance->toArray());

        $instance->limit(2);
        $this->assertSame(['foo', 'bar'], $instance->toArray());
    }

    public function testWithBothOffsetAndLimitThePaginatorProvidesGoodResults()
    {
        $instance = new InPlacePaginatorAdapter(new \ArrayIterator(['foo', 'bar', 'baz', 'qux', 'norf']));

        $instance->limit(3, 1);
        $this->assertSame([1 => 'bar', 2 => 'baz', 3 => 'qux'], $instance->toArray());

        $instance->limit(0, 1);
        $this->assertSame([], $instance->toArray());

        $instance->limit(1, 3);
        $this->assertSame([3 => 'qux'], $instance->toArray());

        $instance->limit(2, 2);
        $this->assertSame([2 => 'baz', 3 => 'qux'], $instance->toArray());
    }

    public function testIteratorCountTotalEntries()
    {
        $instance = new InPlacePaginatorAdapter(new \ArrayIterator(['foo', 'bar', 'baz']));
        $this->assertSame(3, $instance->count());

        $instance->limit(1);
        $this->assertSame(3, $instance->count());
    }
}
