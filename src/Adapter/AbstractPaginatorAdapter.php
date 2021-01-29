<?php
namespace ShoppingFeed\Paginator\Adapter;

use ShoppingFeed\Paginator\Value\AbsoluteInt;

abstract class AbstractPaginatorAdapter implements
    PaginatorAdapterInterface,
    CurrentPageAwareInterface,
    TotalPagesAwareInterface
{
    private ?AbsoluteInt $currentPage = null;
    private ?AbsoluteInt $limit = null;
    private ?AbsoluteInt $offset = null;
    private ?int $totalPages = null;

    public function limit($limit = null, $offset = null): void
    {
        $this->limit  = null;
        $this->offset = null;

        if (null !== $limit) {
            $this->limit = new AbsoluteInt($limit);
        }
        if (null !== $offset) {
            $this->offset = new AbsoluteInt($offset);
        }
    }

    public function setTotalPages(int $total): void
    {
        $this->totalPages = $total;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function setCurrentPage(AbsoluteInt $page): void
    {
        $this->currentPage = $page;
    }

    protected function getCurrentPage(): int
    {
        return $this->currentPage->toInt();
    }

    protected function getLimit(): ?int
    {
        if ($this->limit) {
            return $this->limit->toInt();
        }

        return null;
    }

    protected function getOffset(): ?int
    {
        if ($this->offset) {
            return $this->offset->toInt();
        }

        return null;
    }

    public function toArray(): array
    {
        return iterator_to_array($this);
    }
}
