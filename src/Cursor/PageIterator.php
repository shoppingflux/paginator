<?php

namespace ShoppingFeed\Paginator\Cursor;

use Generator;
use IteratorAggregate;
use ShoppingFeed\Paginator\Exception;

class PageIterator implements IteratorAggregate
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
}
