<?php

namespace ShoppingFeed\Paginator\Adapter;

use ArrayIterator;
use Iterator;
use IteratorAggregate;
use LimitIterator;
use ShoppingFeed\Paginator\Exception;

class InPlacePaginatorAdapter extends AbstractPaginatorAdapter
{
    /** @var \Iterator|\IteratorAggregate */
    private $traversable;

    /** @param iterable|array|\Iterator|\IteratorAggregate $iterator */
    public function __construct($iterator)
    {
        if (is_array($iterator)) {
            $iterator = new ArrayIterator($iterator);
        }

        if (! $iterator instanceof Iterator && ! $iterator instanceof IteratorAggregate) {
            throw Exception\InvalidArgumentException::with(Iterator::class, $iterator);
        }

        $this->traversable = $iterator;
    }

    public function getIterator(): Iterator
    {
        if ($this->getLimit() === 0) {
            return new ArrayIterator([]);
        }

        $iterator = $this->traversable;

        if ($iterator instanceof IteratorAggregate) {
            $iterator = $iterator->getIterator();
        }

        return new LimitIterator(
            $iterator,
            $this->getOffset() ?: 0,
            $this->getLimit() ?: -1,
        );
    }

    public function count(): int
    {
        return iterator_count($this->traversable);
    }
}
