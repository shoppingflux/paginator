<?php
namespace ShoppingFeed\Paginator\Value;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Paginator\Exception;

/**
 * @group paginator
 * @group value
 */
class AbsoluteIntTest extends TestCase
{
    public function testAcceptToStringObjects()
    {
        $object   = new \SimpleXMLElement('<xml>1</xml>');
        $instance = new AbsoluteInt($object);

        $this->assertSame((string) $object, (string) $instance);
        $this->assertSame(1, $instance->toInt());

        $this->expectException(Exception\DomainException::class);
        new AbsoluteInt(new \SimpleXMLElement('<xml>-1</xml>'));
    }

    public function testAcceptScalarValues()
    {
        $instance = new AbsoluteInt('1');
        $this->assertSame(1, $instance->toInt());

        $instance = new AbsoluteInt(1.0);
        $this->assertSame(1, $instance->toInt());

        $instance = new AbsoluteInt(0);
        $this->assertSame(0, $instance->toInt());

        $this->expectException(Exception\DomainException::class);
        new AbsoluteInt(-1);
    }

    public function testRefuseNonScalarValue()
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        new AbsoluteInt([1]);
    }
}

