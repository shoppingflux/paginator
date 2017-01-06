<?php
namespace ShoppingFeed\Paginator;

use ShoppingFeed\Paginator\Adapter\AbstractPaginatorAdapter;
use ShoppingFeed\Paginator\Adapter\InPlacePaginatorAdapter;
use ShoppingFeed\Paginator\Adapter\PaginatorAdapterInterface;
use ShoppingFeed\Paginator\Value\AbsoluteInt;

/**
 * @group paginator
 * @group library
 */
class PaginatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Paginator
     */
    private $instance;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $adapter;

    public function setUp()
    {
        $this->adapter  = $this->createMock(PaginatorAdapterInterface::class);
        $this->instance = new Paginator($this->adapter);
    }

    public function testCurrentPageAccessor()
    {
        $this->assertSame(1, $this->instance->getCurrentPage());
        $this->assertSame($this->instance, $this->instance->setCurrentPage(2));
        $this->assertSame(2, $this->instance->getCurrentPage());
    }

    public function testItemsPerPageAccessor()
    {
        $this->assertSame(10, $this->instance->getItemsPerPage());
        $this->assertSame($this->instance, $this->instance->setItemsPerPage(20));
        $this->assertSame(20, $this->instance->getItemsPerPage());
    }

    public function testInitWithPaginationProvider()
    {
        $provider = $this->createMock(PaginationProviderInterface::class);
        $provider
            ->expects($this->once())
            ->method('getItemsPerPage')
            ->willReturn(15);

        $provider
            ->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn(3);

        $this->assertSame($this->instance, $this->instance->initWith($provider));
        $this->assertSame(15, $this->instance->getItemsPerPage());
        $this->assertSame(3, $this->instance->getCurrentPage());
    }

    public function testCountForwardCallToAdapter()
    {
        $this->adapter
            ->expects($this->once())
            ->method('limit')
            ->with(10, 0)
        ;

        $this->adapter
            ->expects($this->once())
            ->method('count')
            ->willReturn(100)
        ;

        $this->assertSame(100, $this->instance->getTotalCount());
    }

    public function testGetItemsAsArray()
    {
        $this->adapter
            ->expects($this->once())
            ->method('limit')
            ->with(10, 0)
        ;

        $this->adapter
            ->expects($this->once())
            ->method('toArray')
            ->willReturn($expected = ['one', 'two'])
        ;

        $this->assertSame($expected, $this->instance->toArray());
    }

    public function testGetIterator()
    {
        $this->adapter
            ->expects($this->once())
            ->method('limit')
            ->with(10, 0)
        ;

        $this->adapter
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator($expected = [1, 2]))
        ;

        $this->assertSame($expected, iterator_to_array($this->instance));
    }

    public function testGetIteratorWithEmbeddedProcessor()
    {
        $this->adapter
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([1, 2]))
        ;

        $this->instance->setProcessor(function() {
            return 3;
        });

        $this->assertSame([3, 3], iterator_to_array($this->instance));
    }

    public function testCountReturnTheTotalNumberOfPages()
    {
        $this->adapter
            ->expects($this->once())
            ->method('limit')
            ->with(10, 0)
        ;

        $this->adapter
            ->expects($this->once())
            ->method('count')
            ->willReturn(100)
        ;

        $this->assertSame(10, count($this->instance));
    }

    /**
     * @dataProvider nextPageDataProvider
     */
    public function testGetNextPage($currentPage, $expectedPage)
    {
        $adapter  = new InPlacePaginatorAdapter(new \ArrayIterator(array_fill(0, 100, true)));
        $instance = new Paginator($adapter);

        $instance->setItemsPerPage(20);
        $instance->setCurrentPage($currentPage);
        $this->assertSame($expectedPage, $instance->getNextPage());
    }

    public function nextPageDataProvider()
    {
        return [
            [5, null],
            [4, 5],
            [3, 4],
            [2, 3],
            [1, 2]
        ];
    }

    /**
     * @dataProvider prevPageDataProvider
     */
    public function testGetPrevPage($currentPage, $expectedPage)
    {
        $adapter  = new InPlacePaginatorAdapter(new \ArrayIterator(array_fill(0, 100, true)));
        $instance = new Paginator($adapter);

        $instance->setItemsPerPage(20);
        $instance->setCurrentPage($currentPage);
        $this->assertSame($expectedPage, $instance->getPrevPage());
    }

    public function testSetCurrentPageWithCurrentPageAwareAdapter()
    {
        $mock = $this->createMock(AbstractPaginatorAdapter::class);
        $mock
            ->expects($this->once())
            ->method('setCurrentPage')
        ;

        new Paginator($mock);
    }

    public function prevPageDataProvider()
    {
        return [
            [5, 4],
            [4, 3],
            [3, 2],
            [2, 1],
            [1, null],
            [0, null]
        ];
    }
}

