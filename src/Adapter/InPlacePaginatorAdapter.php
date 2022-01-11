<?php
namespace ShoppingFeed\Paginator\Adapter;

use ShoppingFeed\Paginator\Exception;

class InPlacePaginatorAdapter extends AbstractPaginatorAdapter
{
    /**
     * @var \Iterator|\IteratorAggregate
     */
    private $traversable;

    /**
     * @param iterable|array|\Iterator|\IteratorAggregate $iterator
     */
    public function __construct($iterator)
    {
        if (is_array($iterator)) {
            $iterator = new \ArrayIterator($iterator);
        }

        if (! $iterator instanceof \Iterator && ! $iterator instanceof \IteratorAggregate) {
            throw Exception\InvalidArgumentException::with(\Iterator::class, $iterator);
        }

        $this->traversable = $iterator;
    }

    public function getIterator(): \Iterator
    {
        if ($this->getLimit() === 0) {
            return new \ArrayIterator([]);
        }

        $iterator = $this->traversable;
        if ($iterator instanceof \IteratorAggregate) {
            $iterator = $iterator->getIterator();
        }

        $limit = (int) $this->getLimit();

        return new \LimitIterator(
            $iterator,
            $this->getOffset(),
            $limit ?: -1
        );
    }

    public function count(): int
    {
        return iterator_count($this->traversable);
    }
}
