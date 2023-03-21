<?php
namespace ShoppingFeed\Paginator;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Paginator\Exception\InvalidArgumentException;

class CursorSerializableTest extends TestCase
{
    private CursorSerializable $instance;

    public function setUp(): void
    {
        $this->instance = new CursorSerializable();
    }

    public function testDefaultObjectState(): void
    {
        $this->assertSame(10, $this->instance->getLimit());
        $this->assertSame('next', $this->instance->getDirection());
        $this->assertNull($this->instance->getValue());
    }

    public function testWithValue(): void
    {
        $cursor      = CursorSerializable::forward(20);
        $cursorClone = $cursor->withValue('test');

        $this->assertSame(20, $cursor->getLimit());
        $this->assertSame('next', $cursor->getDirection());
        $this->assertSame('', $cursor->getValue());

        $this->assertSame(20, $cursorClone->getLimit());
        $this->assertSame('next', $cursorClone->getDirection());
        $this->assertSame('test', $cursorClone->getValue());
    }

    public function testHydrateFromSerializedString(): void
    {
        $input    = base64_encode(json_encode(['value' => 'a', 'page' => 'next', 'limit' => 3]));
        $instance = $this->instance->withString($input);

        $this->assertSame('a', $instance->getValue());
        $this->assertSame('next', $instance->getDirection());
        $this->assertSame(3, $instance->getLimit());

        $this->assertSame($input, $instance->toString());
    }

    public function testHydrateWithInvalidBase64(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->instance->withString('$$$');
    }

    public function testHydrateWithInvalidJson(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->instance->withString(base64_encode('{m:t}'));
    }

    public function testHydrateWithLimit(): void
    {
        $this->assertSame(
            1,
            $this->instance->withString(base64_encode('{"limit": 1}'))->getLimit()
        );

        $this->expectException(InvalidArgumentException::class);
        $this->instance->withString(base64_encode('{"limit": -1}'));
    }

    public function testHydrateWithPage(): void
    {
        $this->assertSame(
            'next',
            $this->instance->withString(base64_encode('{"page": "next"}'))->getDirection()
        );

        $this->assertSame(
            'prev',
            $this->instance->withString(base64_encode('{"page": "prev"}'))->getDirection()
        );

        $this->expectException(InvalidArgumentException::class);
        $this->instance->withString(base64_encode('{"page": "after"}'));
    }

    public function testUpgradeLimit(): void
    {
        $this->assertSame(10, $this->instance->getLimit());

        $copy = $this->instance->withLimit(5);
        $this->assertSame(5, $copy->getLimit());
        $this->assertSame(10, $this->instance->getLimit());
    }
}
