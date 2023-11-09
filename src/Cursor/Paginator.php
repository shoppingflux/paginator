<?php

namespace ShoppingFeed\Paginator\Cursor;

use Generator;
use IteratorAggregate;
use ShoppingFeed\Iterator\CountableTraversable;
use ShoppingFeed\Paginator\Exception;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Cursor Paginator allow to iterate over a list of result
 */
class Paginator implements IteratorAggregate, CountableTraversable
{
    protected PageDiscoveryInterface $first;

    protected EventDispatcherInterface $dispatcher;

    public function __construct(
        PageDiscoveryInterface $page,
        EventDispatcherInterface $dispatcher = null
    ) {
        if (null === $dispatcher) {
            $dispatcher = new EventDispatcher();
        }

        $this->first      = $page;
        $this->dispatcher = $dispatcher;
    }

    public function getEventDispatcher(): EventDispatcher
    {
        return $this->dispatcher;
    }

    public function getIterator(): Generator
    {
        $number = 0;
        $page   = $this->first;

        do {
            try {
                foreach ($page as $item) {
                    yield $item;
                }
            } catch (Exception\BreakIterationException $exception) {
                break;
            }

            $this->dispatcher->dispatch(new PageScrolledEvent(++$number), PageScrolledEvent::NAME);
        } while ($page = $page->getNextPage());
    }

    public function count(): int
    {
        return $this->first->getTotalCount();
    }
}
