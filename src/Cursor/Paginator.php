<?php

namespace ShoppingFeed\Paginator\Cursor;

use Generator;
use IteratorAggregate;
use ShoppingFeed\Event\Event;
use ShoppingFeed\Event\EventDispatcher;
use ShoppingFeed\Event\EventDispatcherInterface;
use ShoppingFeed\Event\ListenerRegistryInterface;
use ShoppingFeed\Exception\InvalidArgumentException;
use ShoppingFeed\Iterator\CountableTraversable;
use ShoppingFeed\Paginator\Exception;

/**
 * Cursor Paginator allow to iterate over a list of result
 */
class Paginator implements IteratorAggregate, CountableTraversable
{
    public const EVENT_PAGE_SCROLLED = 'paginator.page.scrolled';

    protected PageDiscoveryInterface $first;

    /** @var (EventDispatcherInterface&ListenerRegistryInterface)|null */
    protected EventDispatcherInterface $dispatcher;

    /**
     * @param (EventDispatcherInterface&ListenerRegistryInterface)|null $dispatcher
     */
    public function __construct(
        PageDiscoveryInterface $page,
        EventDispatcherInterface $dispatcher = null,
    ) {
        if (null === $dispatcher) {
            $dispatcher = new EventDispatcher();
        }

        if (! $dispatcher instanceof ListenerRegistryInterface) {
            throw new InvalidArgumentException(
                'Dispatcher must be instance of ' . ListenerRegistryInterface::class,
            );
        }

        $this->first      = $page;
        $this->dispatcher = $dispatcher;
    }

    public function getListenerRegistry(): ListenerRegistryInterface
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

            $this->dispatcher->trigger(
                new Event(self::EVENT_PAGE_SCROLLED, [
                    'page' => ++$number,
                ]),
            );
        } while ($page = $page->getNextPage());
    }

    public function count(): int
    {
        return $this->first->getTotalCount();
    }
}
