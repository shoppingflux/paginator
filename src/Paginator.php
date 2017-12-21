<?php
namespace ShoppingFeed\Paginator;

use ShoppingFeed\Iterator\FilterAggregateAwareTrait;
use ShoppingFeed\Paginator\Adapter\CurrentPageAwareInterface;
use ShoppingFeed\Paginator\Adapter\PaginatorAdapterInterface;
use ShoppingFeed\Paginator\Value\AbsoluteInt;

class Paginator implements PaginationProviderInterface, PaginatorInterface
{
    use FilterAggregateAwareTrait;

    /**
     * @var PaginatorAdapterInterface
     */
    private $adapter;

    /**
     * @var AbsoluteInt
     */
    private $currentPage;

    /**
     * @var AbsoluteInt
     */
    private $itemsPerPage;

    /**
     * Cache the adapter count() call by default
     *
     * @var int
     */
    private $totalCount;

    /**
     * @param PaginatorAdapterInterface $adapter        Paginator adapter
     * @param int                       $perPage        Default number of elements to fetch per page
     * @param int                       $currentPage    The starting point of the internal iterator
     */
    public function __construct(PaginatorAdapterInterface $adapter, $perPage = 10, $currentPage = 1)
    {
        $this->adapter = $adapter;
        $this->setCurrentPage((int) $currentPage);
        $this->setItemsPerPage((int) $perPage);
    }

    /**
     * @param int|AbsoluteInt $number
     *
     * @return $this
     */
    public function setItemsPerPage($number)
    {
        if (! $number instanceof AbsoluteInt) {
            $number = new AbsoluteInt(max(1, $number));
        }

        $this->itemsPerPage = $number;

        return $this;
    }

    /**
     * @param int|AbsoluteInt $number
     *
     * @return $this
     */
    public function setCurrentPage($number)
    {
        if (! $number instanceof AbsoluteInt) {
            $number = new AbsoluteInt(max(1, $number));
        }

        $this->currentPage = $number;

        if ($this->adapter instanceof CurrentPageAwareInterface) {
            $this->adapter->setCurrentPage($number);
        }

        return $this;
    }

    /**
     * Init pagination with provider
     *
     * @param PaginationProviderInterface $provider
     *
     * @return $this
     */
    public function initWith(PaginationProviderInterface $provider)
    {
        $this->setItemsPerPage($provider->getItemsPerPage());
        $this->setCurrentPage($provider->getCurrentPage());

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage->toInt();
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage->toInt();
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        $this->paginate();
        foreach ($this->adapter as $item) {
            foreach ($this->filters as $filter) {
                $item = $filter($item);
            }
            yield $item;
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $this->paginate();

        return $this->adapter->toArray();
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        $this->paginate();

        if (null === $this->totalCount) {
            $this->totalCount = $this->adapter->count();
        }

        return $this->totalCount;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return $this->getTotalCount();
    }

    /**
     * @inheritdoc
     */
    public function getNextPage()
    {
        $current = $this->getCurrentPage();
        if ($current < $this->getTotalPages()) {
            return $current + 1;
        }
    }

    /**
     * @inheritdoc
     */
    public function getPrevPage()
    {
        $prev = $this->getCurrentPage() - 1;
        if ($prev > 0) {
            return $prev;
        }
    }

    /**
     * @inheritDoc
     */
    public function getTotalPages()
    {
        if (! $numberOfItems = $this->getTotalCount()) {
            return 0;
        }

        return (int) ceil($numberOfItems / $this->getItemsPerPage());
    }

    /**
     * Initialize adapter pagination
     */
    private function paginate()
    {
        $perPage = $this->itemsPerPage->toInt();
        $offset  = max(0, ($this->currentPage->toInt() - 1)) * $perPage;

        $this->adapter->limit($perPage, $offset);
    }
}
