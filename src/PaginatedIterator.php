<?php
namespace ShoppingFeed\Paginator;

use ShoppingFeed\Iterator\AbstractIterator;

class PaginatedIterator extends AbstractIterator implements PaginatorInterface
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @param PaginatorInterface $paginator
     */
    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        $currentPage = $this->getCurrentPage();
        $totalPages  = $this->count();

        while($currentPage < $totalPages) {
            foreach ($this->paginator as $key => $item) {
                yield $item;
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
}
