<?php
namespace ShoppingFeed\Paginator\Adapter;

use ShoppingFeed\Paginator\Value\AbsoluteInt;

abstract class AbstractPaginatorAdapter implements PaginatorAdapterInterface
{
    /**
     * @var AbsoluteInt
     */
    private $limit;

    /**
     * @var AbsoluteInt
     */
    private $offset;

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return $this
     */
    public function limit($limit = null, $offset = null)
    {
        $this->limit  = null;
        $this->offset = null;

        if (null !== $limit) {
            $this->limit = new AbsoluteInt($limit);
        }
        if (null !== $offset) {
            $this->offset = new AbsoluteInt($offset);
        }

        return $this;
    }

    /**
     * @return int
     */
    protected function getLimit()
    {
        if ($this->limit) {
            return $this->limit->toInt();
        }
    }

    /**
     * @return int
     */
    protected function getOffset()
    {
        if ($this->offset) {
            return $this->offset->toInt();
        }
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return iterator_to_array($this);
    }
}
