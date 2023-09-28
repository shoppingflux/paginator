<?php

namespace ShoppingFeed\Paginator;

use Throwable;

class CursorSerializable implements CursorInterface
{
    protected const PROP_DIRECTION = 'page';
    protected const PROP_VALUE     = 'value';
    protected const PROP_LIMIT     = 'limit';
    protected const LIMIT_DEFAULT  = 10;
    protected const PAGE_NEXT      = 'next';
    protected const PAGE_PREV      = 'prev';
    protected const DIRECTIONS     = [self::PAGE_PREV, self::PAGE_NEXT];

    /**
     * Sorting direction of the cursor, can be either "before" or "after"
     */
    private string $direction = self::PAGE_NEXT;

    /**
     * Sorting direction of the cursor, can be either "before" or "after"
     */
    private int $limit = self::LIMIT_DEFAULT;

    /**
     * Value of the cursor field
     */
    private ?string $value = null;

    public static function forward(int $limit, string $value = ''): self
    {
        $instance = new self();
        $instance->setValue($value);
        $instance->setLimit($limit);
        $instance->setDirection(self::PAGE_NEXT);

        return $instance;
    }

    public function toString(): string
    {
        return base64_encode(
            json_encode([
                self::PROP_VALUE     => $this->getValue(),
                self::PROP_DIRECTION => $this->getDirection(),
                self::PROP_LIMIT     => $this->getLimit(),
            ], JSON_THROW_ON_ERROR),
        );
    }

    /**
     * Current implementation expects an json string encoded in base64 format.
     * Expected data structure is an array composed of:
     * - value (string, required) : Value of the cursor position
     * - direction (string, required) : next or prev
     * - limit (int, optional) : number of items to get with the cursor
     *
     * @return static The current instance being used
     */
    public function withString(string $serialized): self
    {
        if (! $decoded = base64_decode($serialized)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s : value is not a valid base64 string.',
                $serialized,
            ));
        }

        try {
            $data = json_decode($decoded, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Json decoding failed for "%s" : %s',
                $serialized,
                $exception->getMessage(),
            ));
        }

        $copy = clone $this;
        $copy->setValue($data[self::PROP_VALUE] ?? $copy->value);
        $copy->setLimit($data[self::PROP_LIMIT] ?? $copy->limit);
        $copy->setDirection($data[self::PROP_DIRECTION] ?? $copy->direction);

        return $copy;
    }

    public function withLimit(int $limit): self
    {
        $copy = clone $this;
        $copy->setLimit($limit);

        return $copy;
    }

    public function withValue(string $value): self
    {
        $cursor = clone $this;
        $cursor->setValue($value);

        return $cursor;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    final protected function setValue(?string $value): void
    {
        $this->value = $value;
    }

    final protected function setDirection(string $direction): void
    {
        if (! in_array($direction, self::DIRECTIONS, true)) {
            throw new Exception\InvalidArgumentException(
                'Invalid direction given, it must be one of the following: ' . implode(', ', self::DIRECTIONS),
            );
        }

        $this->direction = $direction;
    }

    final protected function setLimit(int $limit): void
    {
        if ($limit < 0) {
            throw new Exception\InvalidArgumentException(
                'Invalid limit given, it must be an integer greater than or equal to 0',
            );
        }

        $this->limit = $limit;
    }
}
