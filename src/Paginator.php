<?php
namespace ShoppingFeed\Paginator;

use ShoppingFeed\Iterator\FilterAggregateAwareTrait;
use ShoppingFeed\Paginator\Adapter\CurrentPageAwareInterface;
use ShoppingFeed\Paginator\Adapter\PaginatorAdapterInterface;
use ShoppingFeed\Paginator\Value\AbsoluteInt;

class Paginator implements PaginationProviderInterface, PaginatorInterface
{
    use FilterAggregateAwareTrait;

    private PaginatorAdapterInterface $adapter;

    private AbsoluteInt $currentPage;

    private AbsoluteInt $itemsPerPage;

    /**
     * Cache the adapter count() call by default
     */
    private ?int $totalCount = null;

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
    public function setItemsPerPage($number): self
    {
        if (! $number instanceof AbsoluteInt) {
            $number = new AbsoluteInt(max(1, $number));
        }

        $this->itemsPerPage = $number;

        return $this;
    }

    /**
     * Init pagination with provider
     */
    public function initWith(PaginationProviderInterface $provider): self
    {
        $this->setItemsPerPage($provider->getItemsPerPage());
        $this->setCurrentPage($provider->getCurrentPage());

        return $this;
    }

    /**
     * @param int|AbsoluteInt $number
     *
     * @return $this
     */
    public function setCurrentPage($number): self
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

    public function getCurrentPage(): int
    {
        return $this->currentPage->toInt();
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage->toInt();
    }

    public function getIterator(): \Iterator
    {
        $this->paginate();
        foreach ($this->adapter as $item) {
            foreach ($this->filters as $filter) {
                $item = $filter($item);
            }
            yield $item;
        }
    }

    public function toArray(): array
    {
        return iterator_to_array($this->getIterator());
    }

    public function getTotalCount(): int
    {
        $this->paginate();

        if (null === $this->totalCount) {
            $this->totalCount = $this->adapter->count();
        }

        return $this->totalCount;
    }

    public function count(): int
    {
        return $this->getTotalCount();
    }

    public function getNextPage(): ?int
    {
        $current = $this->getCurrentPage();
        if ($current < $this->getTotalPages()) {
            return $current + 1;
        }

        return null;
    }

    public function getPrevPage(): ?int
    {
        $prev = $this->getCurrentPage() - 1;
        if ($prev > 0) {
            return $prev;
        }

        return null;
    }

    public function getTotalPages(): int
    {
        if (! $numberOfItems = $this->getTotalCount()) {
            return 0;
        }

        return (int) ceil($numberOfItems / $this->getItemsPerPage());
    }

    /**
     * Initialize adapter pagination
     */
    private function paginate(): void
    {
        $perPage = $this->itemsPerPage->toInt();
        $offset  = max(0, ($this->currentPage->toInt() - 1)) * $perPage;

        $this->adapter->limit($perPage, $offset);
    }
}
