<?php
namespace ShoppingFeed\Paginator;

use ShoppingFeed\Paginator\Exception\BreakIterationException;

/**
 * @group paginator
 * @group library
 */
class PaginatedIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PaginatedIterator
     */
    private $instance;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $adapter;

    public function setUp()
    {
        $this->adapter  = $this->createMock(PaginatorInterface::class);
        $this->instance = new PaginatedIterator($this->adapter);
    }

    public function testCurrentAccessorForwardToAdapter()
    {
        $this->adapter
            ->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn(10);

        $this->assertSame(10, $this->instance->getCurrentPage());

        $this->adapter
            ->expects($this->once())
            ->method('setCurrentPage')
            ->willReturn(2);

        $this->assertSame($this->instance, $this->instance->setCurrentPage(2));
    }

    public function testItemsPerPageAccessorForwardToAdapter()
    {
        $this->adapter
            ->expects($this->once())
            ->method('getItemsPerPage')
            ->willReturn(20);

        $this->assertSame(20, $this->instance->getItemsPerPage());

        $this->adapter
            ->expects($this->once())
            ->method('setItemsPerPage')
            ->willReturn(5);

        $this->assertSame($this->instance, $this->instance->setItemsPerPage(5));
    }

    public function testCountTotalItemsForwardToAdapter()
    {
        $this->adapter
            ->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(33);

        $this->assertSame(33, $this->instance->getTotalCount());
    }

    public function testCountForwardToAdapter()
    {
        $this->adapter
            ->expects($this->once())
            ->method('count')
            ->willReturn(33);

        $this->assertSame(33, $this->instance->count());
    }

    public function testGetNextPageForwardToAdapter()
    {
        $this->adapter
            ->expects($this->once())
            ->method('getNextPage')
            ->willReturn(33);

        $this->assertSame(33, $this->instance->getNextPage());
    }

    public function testGetPrevPageForwardToAdapter()
    {
        $this->adapter
            ->expects($this->once())
            ->method('getPrevPage')
            ->willReturn(33);

        $this->assertSame(33, $this->instance->getPrevPage());
    }

    /**
     * @dataProvider iteratesOverPaginatorDataProvider
     */
    public function testIteratesOverPaginatedAdapter($currentPage, $total, $expected)
    {
        $this->adapter
            ->expects($this->once())
            ->method('getTotalPages')
            ->willReturn($total)
        ;

        $this->adapter
            ->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn($currentPage)
        ;

        $this->adapter
            ->expects($this->exactly($expected))
            ->method('setCurrentPage')
        ;

        $this->adapter
            ->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([1 => 'test']))
        ;

        $this->instance->toArray();
    }

    public function iteratesOverPaginatorDataProvider()
    {
        return [
            [1, 10, 9],
            [1, 1, 0],
            [1, 2, 1],
            [3, 4, 1],
            [4, 4, 0],
            [0, 0, 0],
        ];
    }

    public function testAddFilterForwardToInnerPaginator()
    {
        $this->adapter
            ->expects($this->once())
            ->method('addFilter')
            ->with('is_array');

        $this->assertSame($this->instance, $this->instance->addFilter('is_array'));
    }

    public function testIterationCanBeBrokenWithAppropriateException()
    {
        $this->adapter
            ->expects($this->once())
            ->method('getIterator')
            ->willThrowException(new BreakIterationException());

        $this->adapter
            ->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn(1);

        $this->adapter
            ->expects($this->once())
            ->method('getTotalPages')
            ->willReturn(100);

        $this->instance->toArray();
    }
}

