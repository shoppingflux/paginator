<?php

namespace ShoppingFeed\Paginator;

use IteratorAggregate;

/**
 * Offset based instead of page based paginator.
 */
class OffsetPaginator extends AbstractPaginator implements IteratorAggregate
{
    private ?int $offset = null;

    public function __construct(Adapter\PaginatorAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function toArray(): array
    {
        return $this->adapter->toArray();
    }

    public function setOffset(?int $offset): void
    {
        $this->offset = $offset;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getNextOffset(): ?int
    {
        $total = $this->getTotalCount();
        $next  = min($total, $this->offset + $this->limit);

        if ($next < $total) {
            return $next;
        }

        return null;
    }

    public function getPrevOffset(): ?int
    {
        $offset = $this->getOffset();

        if (! $offset) {
            return null;
        }

        return max(0, $offset - $this->limit);
    }

    protected function paginate(): void
    {
        $this->adapter->limit(
            $this->limit,
            $this->offset
        );
    }
}
