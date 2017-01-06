<?php
namespace ShoppingFeed\Paginator;

use ShoppingFeed\Paginator\Adapter\CurrentPageAwareInterface;
use ShoppingFeed\Paginator\Adapter\PaginatorAdapterInterface;
use ShoppingFeed\Paginator\Value\AbsoluteInt;

class Paginator implements PaginationProviderInterface, PaginatorInterface
{
    /**
     * @var callable
     */
    private $processor;

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
     * @param PaginatorAdapterInterface $adapter
     */
    public function __construct(PaginatorAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->setCurrentPage(1);
        $this->setItemsPerPage(10);
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
        $processor = $this->getProcessor();

        foreach ($this->adapter as $item) {
            if (null !== $processor) {
                $item = $processor($item);
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

        return $this->adapter->count();
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return (int) ceil($this->getTotalCount() / $this->getItemsPerPage());
    }

    /**
     * @inheritdoc
     */
    public function getNextPage()
    {
        $current = $this->getCurrentPage();
        if ($current < $this->count()) {
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
     * @return callable
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * @param callable $processor
     *
     * @return $this
     */
    public function setProcessor(callable $processor)
    {
        $this->processor = $processor;

        return $this;
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
