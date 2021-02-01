<?php
namespace ShoppingFeed\Paginator;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Paginator\Adapter\InPlacePaginatorAdapter;
use ShoppingFeed\Paginator\Exception\BreakIterationException;

class PaginatedIteratorTest extends TestCase
{
    private PaginatedIterator $instance;

    private PaginatorInterface $adapter;

    public function setUp(): void
    {
        $this->adapter  = $this->createMock(PaginatorInterface::class);
        $this->instance = new PaginatedIterator($this->adapter);
    }

    public function testCurrentAccessorForwardToAdapter(): void
    {
        $this->adapter
            ->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn(10);

        $this->assertSame(10, $this->instance->getCurrentPage());

        $this->adapter
            ->expects($this->once())
            ->method('setCurrentPage')
            ->with(2);

        $this->assertSame($this->instance, $this->instance->setCurrentPage(2));
    }

    public function testItemsPerPageAccessorForwardToAdapter(): void
    {
        $this->adapter
            ->expects($this->once())
            ->method('getItemsPerPage')
            ->willReturn(20);

        $this->assertSame(20, $this->instance->getItemsPerPage());

        $this->adapter
            ->expects($this->once())
            ->method('setItemsPerPage')
            ->with(5);

        $this->assertSame($this->instance, $this->instance->setItemsPerPage(5));
    }

    public function testCountTotalItemsForwardToAdapter(): void
    {
        $this->adapter
            ->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(33);

        $this->assertSame(33, $this->instance->getTotalCount());
    }

    public function testCountForwardToAdapter(): void
    {
        $this->adapter
            ->expects($this->once())
            ->method('count')
            ->willReturn(33);

        $this->assertSame(33, $this->instance->count());
    }

    public function testGetNextPageForwardToAdapter(): void
    {
        $this->adapter
            ->expects($this->once())
            ->method('getNextPage')
            ->willReturn(33);

        $this->assertSame(33, $this->instance->getNextPage());
    }

    public function testGetPrevPageForwardToAdapter(): void
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
    public function testIteratesOverPaginatedAdapter($currentPage, $total, $expected): void
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

    public function iteratesOverPaginatorDataProvider(): array
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

    public function testAddFilterForwardToInnerPaginator(): void
    {
        $this->adapter
            ->expects($this->once())
            ->method('addFilter')
            ->with('is_array');

        $this->assertSame($this->instance, $this->instance->addFilter('is_array'));
    }

    public function testIterationCanBeBrokenWithAppropriateException(): void
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

    public function testCanBeBuiltFromAdapter(): void
    {
        $expected = range(1, 10);
        $instance = PaginatedIterator::withAdapter(new InPlacePaginatorAdapter($expected));

        $this->assertSame($expected, $instance->toArray());
    }

    /**
     * @dataProvider pageFilterDataProvider
     */
    public function testFiltersCanBeAppliedAtBatchLevel($perPage, $expected): void
    {
        // Starts with 10 items with value = 0
        $items    = array_fill(0, 3, 0);
        $instance = PaginatedIterator::withAdapter(new InPlacePaginatorAdapter($items));

        // Increment items values with page number and total count
        $instance->addPagefilter(function(array $items, $page, $total) {
            return array_map(function($item) use ($page, $total) {
                return $item + $page + $total;
            }, $items);
        });

        $instance->setItemsPerPage($perPage);
        $this->assertSame($expected, $instance->toArray());
    }

    public function pageFilterDataProvider(): array
    {
        return [
            [3, [2, 2, 2]],
            [2, [3, 3, 4]],
            [1, [4, 5, 6]],
        ];
    }
}

