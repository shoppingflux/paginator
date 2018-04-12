<?php
namespace ShoppingFeed\Paginator;

use ShoppingFeed\Iterator\AbstractIterator;
use ShoppingFeed\Paginator\Adapter\PaginatorAdapterInterface;

class PaginatedIterator extends AbstractIterator implements PaginatorInterface
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var callable[]
     */
    private $pageFilters = [];

    /**
     * @param PaginatorInterface $paginator
     */
    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Shortcut that allow to build a new instance directly from an adapter instance
     *
     * @param PaginatorAdapterInterface $adapter
     *
     * @return PaginatedIterator
     */
    public static function withAdapter(PaginatorAdapterInterface $adapter)
    {
        return new self(new Paginator($adapter));
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
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
            $this->paginator->setCurrentPage($currentPage);
        }
    }

    /**
     * @inheritdoc
     */
    public function setCurrentPage($number)
    {
        $this->paginator->setCurrentPage($number);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setItemsPerPage($number)
    {
        $this->paginator->setItemsPerPage($number);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTotalCount()
    {
        return $this->paginator->getTotalCount();
    }

    /**
     * @inheritdoc
     */
    public function getCurrentPage()
    {
        return $this->paginator->getCurrentPage();
    }

    /**
     * @inheritdoc
     */
    public function getItemsPerPage()
    {
        return $this->paginator->getItemsPerPage();
    }

    /**
     * @inheritdoc
     */
    public function getNextPage()
    {
        return $this->paginator->getNextPage();
    }

    /**
     * @inheritdoc
     */
    public function getPrevPage()
    {
        return $this->paginator->getPrevPage();
    }

    /**
     * @inheritDoc
     */
    public function getTotalPages()
    {
        return $this->paginator->getTotalPages();
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return $this->paginator->count();
    }

    /**
     * @inheritDoc
     */
    public function addFilter(callable $processor)
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
    public function addPageFilter(callable $processor)
    {
        $this->pageFilters[] = $processor;

        return $this;
    }
}
