<?php
namespace ShoppingFeed\Paginator;

use PHPUnit\Framework\TestCase;

class CursorEdgeTest extends TestCase
{
    public function testAccessors(): void
    {
        $cursor   = $this->createMock(CursorInterface::class);
        $node     = new \ArrayObject();
        $instance = new CursorEdge($cursor, $node);

        $this->assertSame($cursor, $instance->getCursor());
        $this->assertSame($node, $instance->getNode());
    }
}
