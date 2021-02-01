<?php
namespace ShoppingFeed\Paginator;

use ShoppingFeed\Iterator\FilterAggregateAwareTrait;

abstract class AbstractPaginator implements \Countable
{
    use FilterAggregateAwareTrait;

    protected Adapter\PaginatorAdapterInterface $adapter;

    /**
     * The number of elements per chunk
     */
    protected ?int $limit = null;

    /**
     * Cache the adapter count() call by default
     */
    protected ?int $totalCount = null;

    public function getIterator(): \Iterator
    {
        $this->paginate();

        // Do not perform backend query when no items requested
        if (! $this->limit) {
            return new \ArrayIterator([]);
        }

        foreach ($this->adapter as $key => $item) {
            foreach ($this->filters as $filter) {
                $item = $filter($item);
            }

            yield $key => $item;
        }
    }

    public function getTotalCount(): int
    {
        $this->paginate();

        if (null === $this->totalCount) {
            $this->totalCount = $this->adapter->count();
        }

        return $this->totalCount;
    }

    public function count(): int
    {
        return $this->getTotalCount();
    }

    /**
     * clear persisted totalCount cached property
     */
    public function resetCount(): void
    {
        $this->totalCount = null;
    }

    abstract protected function paginate(): void;
}
