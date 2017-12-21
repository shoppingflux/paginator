<?php
namespace ShoppingFeed\Paginator\Adapter;

use ShoppingFeed\Paginator\Exception;

class InPlacePaginatorAdapter extends AbstractPaginatorAdapter
{
    /**
     * @var \Iterator
     */
    private $traversable;

    /**
     * @param iterable $iterator
     */
    public function __construct(iterable $iterator)
    {
        if (is_array($iterator)) {
            $iterator = new \ArrayIterator($iterator);
        }

        if (! $iterator instanceof \Iterator && ! $iterator instanceof \IteratorAggregate) {
            throw Exception\InvalidArgumentException::with(\Iterator::class, $iterator);
        }

        $this->traversable = $iterator;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        if ($this->getLimit() === 0) {
            return new \ArrayIterator([]);
        }

        $iterator = $this->traversable;
        if ($iterator instanceof \IteratorAggregate) {
            $iterator = $iterator->getIterator();
        }

        return new \LimitIterator(
            $iterator,
            $this->getOffset(),
            $this->getLimit() ?: -1
        );
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return iterator_count($this->traversable);
    }
}
