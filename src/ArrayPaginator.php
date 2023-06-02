<?php

namespace ShoppingFeed\Paginator;

use ArrayIterator;
use IteratorAggregate;

/**
 * This class is a simple implementation of ForwardInterface for array
 *
 * @phpstan-type Item mixed
 * @phpstan-type Page array<int, Item>
 */
class ArrayPaginator implements ForwardInterface, IteratorAggregate
{
    /** @var array<int, Page> Next pages */
    private array $next;

    /** @var Page */
    private array $current;

    public function __construct(array $pages)
    {
        $this->current = array_shift($pages);
        $this->next    = $pages;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->current);
    }

    public function hasNextPage(): bool
    {
        return ! empty($this->next);
    }

    public function getNextPage(): ?self
    {
        if ($this->hasNextPage()) {
            return new self($this->next);
        }

        return null;
    }
}
