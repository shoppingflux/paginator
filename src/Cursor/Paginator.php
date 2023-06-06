<?php

namespace ShoppingFeed\Paginator\Cursor;

use Generator;
use IteratorAggregate;
use ShoppingFeed\Iterator\CountableTraversable;
use ShoppingFeed\Paginator\Exception;

/**
 * Cursor Paginator allow to iterate over a list of result
 */
class Paginator implements IteratorAggregate, CountableTraversable
{
    protected PageDiscoveryInterface $first;

    public function __construct(PageDiscoveryInterface $page)
    {
        $this->first = $page;
    }

    public function getIterator(): Generator
    {
        $page = $this->first;

        do {
            try {
                foreach ($page as $item) {
                    yield $item;
                }
            } catch (Exception\BreakIterationException $exception) {
                break;
            }
        } while ($page = $page->getNextPage());
    }

    public function count(): int
    {
        return $this->first->getTotalCount();
    }
}
