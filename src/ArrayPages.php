<?php

namespace ShoppingFeed\Paginator;

use ArrayIterator;
use IteratorAggregate;

/**
 * This class is a simple implementation of PageDiscoveryInterface for array
 *
 * @phpstan-type Item mixed
 * @phpstan-type Page array<int, Item>
 */
class ArrayPages implements PageDiscoveryInterface, IteratorAggregate
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

    private function hasNextPage(): bool
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
