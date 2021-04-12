<?php
namespace ShoppingFeed\Paginator;

/**
 * Wrap element (node) with its associated cursor.
 */
class CursorEdge
{
    private CursorInterface $cursor;

    /**
     * @var mixed
     */
    private $node;

    public function __construct(CursorInterface $cursor, $node)
    {
        $this->cursor = $cursor;
        $this->node   = $node;
    }

    public function getCursor(): CursorInterface
    {
        return $this->cursor;
    }

    public function getNode()
    {
        return $this->node;
    }
}
