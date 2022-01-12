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
}
