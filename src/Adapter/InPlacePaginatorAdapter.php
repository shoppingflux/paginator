<?php
namespace ShoppingFeed\Paginator\Adapter;

class InPlacePaginatorAdapter extends AbstractPaginatorAdapter
{
    /**
     * @var \Traversable
     */
    private $traversable;

    /**
     * @param \Iterator $iterator
     */
    public function __construct(\Iterator $iterator)
    {
        $this->traversable = $iterator;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        if ($this->getLimit() === 0) {
            return new \ArrayIterator([]);
        }

        return new \LimitIterator(
            $this->traversable,
            $this->getOffset(),
            $this->getLimit() ?: -1
        );
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return iterator_count($this->traversable);
    }
}
