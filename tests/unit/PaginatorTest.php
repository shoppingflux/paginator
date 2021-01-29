<?php
namespace ShoppingFeed\Paginator;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Paginator\Adapter\AbstractPaginatorAdapter;
use ShoppingFeed\Paginator\Adapter\InPlacePaginatorAdapter;
use ShoppingFeed\Paginator\Adapter\PaginatorAdapterInterface;

class PaginatorTest extends TestCase
{
    /**
     * @var Paginator
     */
    private Paginator $instance;

    private PaginatorAdapterInterface $adapter;

    public function setUp(): void
    {
        $this->adapter  = $this->createMock(PaginatorAdapterInterface::class);
        $this->instance = new Paginator($this->adapter);
    }

    public function testCurrentPageAccessor(): void
    {
        $this->assertSame(1, $this->instance->getCurrentPage());
        $this->assertSame($this->instance, $this->instance->setCurrentPage(2));
        $this->assertSame(2, $this->instance->getCurrentPage());
    }

    public function testItemsPerPageAccessor(): void
    {
        $this->assertSame(10, $this->instance->getItemsPerPage());
        $this->assertSame($this->instance, $this->instance->setItemsPerPage(20));
        $this->assertSame(20, $this->instance->getItemsPerPage());
    }

    public function testInitWithPaginationProvider(): void
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

    public function testCountForwardCallToAdapter(): void
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

    public function testGetItemsAsArray(): void
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

        $this->assertSame($expected, $this->instance->toArray());
    }

    public function testGetIterator(): void
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

    public function testCountReturnTheTotalNumberOfPages(): void
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

        $this->assertSame(100, count($this->instance));
    }

    /**
     * @dataProvider nextPageDataProvider
     */
    public function testGetNextPage($currentPage, $expectedPage): void
    {
        $adapter  = new InPlacePaginatorAdapter(new \ArrayIterator(array_fill(0, 100, true)));
        $instance = new Paginator($adapter);

        $instance->setItemsPerPage(20);
        $instance->setCurrentPage($currentPage);
        $this->assertSame($expectedPage, $instance->getNextPage());
    }

    public function nextPageDataProvider(): array
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
    public function testGetPrevPage($currentPage, $expectedPage): void
    {
        $adapter  = new InPlacePaginatorAdapter(new \ArrayIterator(array_fill(0, 100, true)));
        $instance = new Paginator($adapter);

        $instance->setItemsPerPage(20);
        $instance->setCurrentPage($currentPage);
        $this->assertSame($expectedPage, $instance->getPrevPage());
    }

    public function prevPageDataProvider(): array
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

    public function testSetCurrentPageWithCurrentPageAwareAdapter(): void
    {
        $mock = $this->createMock(AbstractPaginatorAdapter::class);
        $mock
            ->expects($this->once())
            ->method('setCurrentPage')
        ;

        new Paginator($mock);
    }

    /**
     * @dataProvider providesElementsForTotalCount
     */
    public function testGetTotalCountOfPages($perPage, $itemCount, $totalPages): void
    {
        $mock = $this->createMock(PaginatorAdapterInterface::class);
        $mock
            ->expects($this->once())
            ->method('count')
            ->willReturn($itemCount);

        $paginator = new Paginator($mock, $perPage);
        $this->assertSame($totalPages, $paginator->getTotalPages());
    }

    public function providesElementsForTotalCount(): array
    {
        return [
            [10, 0, 0],
            [10, 1, 1],
            [10, 9, 1],
            [10, 10, 1],
            [10, 11, 2],
        ];
    }

    public function testAddFilterIsFluent(): void
    {
        $this->assertSame(
            $this->instance,
            $this->instance->addFilter('is_array')
        );
    }

    public function testFiltersAreAppliedOnIterations(): void
    {
        $this->instance->addFilter(function($item) {
            return $item + 1;
        });
        $this->instance->addFilter(function($item) {
            return $item + 1;
        });

        $this->adapter
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([0, 0]));

        $this->assertSame([2, 2], iterator_to_array($this->instance));
    }
}

