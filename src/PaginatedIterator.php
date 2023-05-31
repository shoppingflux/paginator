<?php

namespace ShoppingFeed\Paginator;

use Iterator;
use ShoppingFeed\Iterator\AbstractIterator;
use ShoppingFeed\Paginator\Adapter\PaginatorAdapterInterface;

class PaginatedIterator extends AbstractIterator implements PaginatorInterface
{
    private PaginatorInterface $paginator;

    /** @var callable[] */
    private array $pageFilters = [];

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Shortcut that allow to build a new instance directly from an adapter instance
     */
    public static function withAdapter(PaginatorAdapterInterface $adapter): self
    {
        return new self(new Paginator($adapter));
    }

    public function getIterator(): Iterator
    {
        $currentPage = $this->getCurrentPage();
        $totalPages  = $this->getTotalPages();

        while ($currentPage <= $totalPages) {
            try {
                $items = $this->paginator;
                // When filters at page level, we must run the current paginator iteration and collect results first.
                // Its done like this because we must avoid to run the same paginator batch more than once,
                // and by this way prevent extra load from the paginator itself (ie: SQL paginator)

                if ($this->pageFilters) {
                    $items = $items->toArray();

                    foreach ($this->pageFilters as $filter) {
                        $items = $filter($items, $currentPage, $totalPages);
                    }
                }

                foreach ($items as $item) {
                    yield $item;
                }
            } catch (Exception\BreakIterationException $exception) {
                break;
            }

            if ($currentPage === $totalPages) {
                break;
            }

            $currentPage += 1;
            $this->setCurrentPage($currentPage);
        }
    }

    public function setCurrentPage($number): self
    {
        $this->paginator->setCurrentPage($number);

        return $this;
    }

    public function setItemsPerPage($number): self
    {
        $this->paginator->setItemsPerPage($number);

        return $this;
    }

    public function getTotalCount(): int
    {
        return $this->paginator->getTotalCount();
    }

    public function getCurrentPage(): int
    {
        return $this->paginator->getCurrentPage();
    }

    public function getItemsPerPage(): int
    {
        return $this->paginator->getItemsPerPage();
    }

    public function getNextPage(): ?int
    {
        return $this->paginator->getNextPage();
    }

    public function getPrevPage(): ?int
    {
        return $this->paginator->getPrevPage();
    }

    public function getTotalPages(): int
    {
        return $this->paginator->getTotalPages();
    }

    public function count(): int
    {
        return $this->paginator->count();
    }

    public function addFilter(callable $processor): self
    {
        $this->paginator->addFilter($processor);

        return $this;
    }

    /**
     * Register filter that apply transformation for each data set returned by the inner paginator.
     * Despite the filter method, this one must expects a traversable (iterable) collection of elements,
     * where the size is corresponding to the items per page defined for pagination.
     *
     * The filter must return an iterable collection of elements, in order to preserve the loop process.
     *
     * @param callable $processor A valid callback that will accepts:
     *                            - (array) $items       : items collection provided by the paginator
     *                            - (int)   $currentPage : the current page number
     *                            - (int)   $totalPages  : the total pages count

     *                            Example:
     *                            $this->addPageFilter(function(array $items, int $current, int $total) {
     *                                return array_map('trim', $items);
     *                            });
     *
     * @return $this
     */
    public function addPageFilter(callable $processor): self
    {
        $this->pageFilters[] = $processor;

        return $this;
    }
}
