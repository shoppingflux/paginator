<?php

namespace ShoppingFeed\Paginator;

interface CursorInterface
{
    /**
     * Build a new instance from serialized string.
     */
    public function withString(string $serialized): self;

    /**
     * Provides a serialized version of the cursor
     */
    public function toString(): string;

    /**
     * Gives the number of items that must be a part of the poll
     */
    public function getLimit(): int;

    /**
     * The value of the field(s) associated with the cursor
     */
    public function getValue(): ?string;

    /**
     * Can be either before or after
     */
    public function getDirection(): string;

    /**
     * Create a new cursor for next pagination with the given value
     */
    public static function forward(int $limit, string $value = ''): self;

    /**
     * Create a new cursor for previous pagination with the given value
     */
    public static function backward(int $limit, string $value = ''): self;

    /**
     * Build a new instance with the given value
     */
    public function withValue(string $value): self;
}
