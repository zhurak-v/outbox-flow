<?php

declare(strict_types=1);

namespace OutboxFlow\Core\Models\Collection;

use ArrayIterator;
use Traversable;

use OutboxFlow\Core\Models\Hashable\Hashable;
use OutboxFlow\Core\Models\Equatable\Equatable;

/**
 * @template T of Hashable&Equatable
 *
 * @implements Collection<T>
 */
final class ArrayCollection implements Collection
{
    /**
     * @var list<T>
     */
    private array $items = [];

    /**
     * @param iterable<T> $items
     */
    public function __construct(iterable $items = [])
    {
        foreach ($items as $item) {
            $this->items[] = $item;
        }
    }

    /**
     * @return self<T>
     */
    public static function empty(): self
    {
        return new self();
    }

    /**
     * @template U of Hashable&Equatable
     *
     * @param iterable<U> $items
     * @return self<U>
     */
    public static function from(iterable $items): self
    {
        return new self($items);
    }

    /**
     * @template U of Hashable&Equatable
     *
     * @param U ...$items
     * @return self<U>
     */
    public static function of(mixed ...$items): self
    {
        return new self($items);
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function size(): int
    {
        return count($this->items);
    }

    /**
     * @param T $item
     */
    public function add(mixed $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @param T $item
     */
    public function remove(mixed $item): bool
    {
        foreach ($this->items as $key => $value) {
            if ($value->equals($item)) {
                unset($this->items[$key]);
                $this->items = array_values($this->items);

                return true;
            }
        }

        return false;
    }

    public function clear(): void
    {
        $this->items = [];
    }

    /**
     * @param T $item
     */
    public function contains(mixed $item): bool
    {
        foreach ($this->items as $value) {
            if ($value->equals($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Traversable<int, T>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @return list<T>
     */
    public function toArray(): array
    {
        return $this->items;
    }
}