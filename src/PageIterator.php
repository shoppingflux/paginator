<?php

namespace ShoppingFeed\Paginator;

use Generator;
use IteratorAggregate;

class PageIterator implements IteratorAggregate
{
    private PageDiscoveryInterface $current;

    public function __construct(PageDiscoveryInterface $cursor)
    {
        $this->current = $cursor;
    }

    public function getIterator(): Generator
    {
        $page = $this->current;

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
}
