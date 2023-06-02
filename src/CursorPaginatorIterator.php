<?php

namespace ShoppingFeed\Paginator;

use Generator;
use IteratorAggregate;

class CursorPaginatorIterator implements IteratorAggregate
{
    private ForwardInterface $cursor;

    public function __construct(ForwardInterface $cursor)
    {
        $this->cursor = $cursor;
    }

    public function getIterator(): Generator
    {
        $page = $this->cursor;

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
