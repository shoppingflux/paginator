<?php

namespace ShoppingFeed\Paginator;

use ShoppingFeed\Paginator\Adapter\CurrentPageAwareInterface;
use ShoppingFeed\Paginator\Adapter\PaginatorAdapterInterface;
use ShoppingFeed\Paginator\Adapter\TotalPagesAwareInterface;
use ShoppingFeed\Paginator\Value\AbsoluteInt;

class Paginator extends AbstractPaginator implements PaginationProviderInterface, PaginatorInterface
{
    private AbsoluteInt $currentPage;

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
            $number = new AbsoluteInt(max(0, $number));
        }

        $this->limit = $number->toInt();

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
        return $this->limit;
    }

    public function toArray(): array
    {
        return iterator_to_array($this->getIterator());
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
            $total = 0;
        } else {
            $total = (int) ceil($numberOfItems / ($this->getItemsPerPage() ?: 1));
        }

        if ($this->adapter instanceof TotalPagesAwareInterface) {
            $this->adapter->setTotalPages($total);
        }

        return $total;
    }

    public function toOffsetPaginator(): OffsetPaginator
    {
        $paginator = new OffsetPaginator($this->adapter);
        $paginator->setLimit($this->getItemsPerPage());
        $paginator->setOffset($this->getItemsPerPage() * $this->getCurrentPage());

        return $paginator;
    }

    /**
     * Initialize adapter pagination
     */
    protected function paginate(): void
    {
        $perPage = $this->limit;
        $offset  = max(0, ($this->currentPage->toInt() - 1)) * $perPage;

        $this->adapter->limit($perPage, $offset);
    }
}
