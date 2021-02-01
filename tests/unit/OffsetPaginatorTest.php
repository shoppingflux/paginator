<?php
namespace ShoppingFeed\Paginator;

use PHP_CodeSniffer\Tests\Core\File\testFECNNamespacedClass;
use PHPUnit\Framework\TestCase;
use ShoppingFeed\Paginator\Adapter\PaginatorAdapterInterface;

class OffsetPaginatorTest extends TestCase
{
    private PaginatorAdapterInterface $adapter;
    private OffsetPaginator $instance;

    public function setUp(): void
    {
        $this->adapter  = $this->createMock(PaginatorAdapterInterface::class);
        $this->instance = new OffsetPaginator($this->adapter);
    }

    public function testLimitAndOffsetAccessors(): void
    {
        $this->assertNull($this->instance->getLimit());
        $this->assertNull($this->instance->getOffset());

        $this->instance->setLimit(10);
        $this->instance->setOffset(3);

        $this->assertSame(10, $this->instance->getLimit());
        $this->assertSame(3, $this->instance->getOffset());
    }

    public function testToArrayForwardToAdapter(): void
    {
        $expected = ['a' => 1];

        $this->adapter
            ->expects($this->once())
            ->method('toArray')
            ->willReturn($expected);

        $this->assertSame($expected, $this->instance->toArray());
    }

    public function testGetNextOffset(): void
    {
        $this->assertNull($this->instance->getNextOffset(), 'no offset be default');

        // configure more for all consecutive calls below
        $this->adapter
            ->expects($this->exactly(2))
            ->method('limit')
            ->withConsecutive([10, 0], [10, 20]);

        $this->instance->resetCount();
        $this->instance->setLimit(10);
        $this->instance->setOffset(0);
        $this->adapter->method('count')->willReturn(20);
        $this->assertSame(10, $this->instance->getNextOffset(), 'The offset match 0+10');

        $this->instance->resetCount();
        $this->instance->setOffset(20);
        $this->assertNull($this->instance->getNextOffset(), 'We reached the end of the page');
    }

    public function testGetPrevOffset(): void
    {
        $this->assertNull($this->instance->getPrevOffset(), 'no offset be default');

        $this->instance->setLimit(10);
        $this->instance->setOffset(15);
        $this->assertSame(5, $this->instance->getPrevOffset());

        $this->instance->setOffset(0);
        $this->assertNull($this->instance->getPrevOffset(), 'no prev offset available');
    }

    public function testGetIteratorForwardToAdapter(): void
    {
        $iterator = new \ArrayIterator(['a' => 'b']);

        $this->adapter
            // expects only once to prevent multiples possible backend calls
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn($iterator);

        $this->instance->setLimit(10);

        $this->assertSame(
            $iterator->getArrayCopy(),
            iterator_to_array($this->instance->getIterator())
        );
    }
}
