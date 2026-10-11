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
final class HashSetCollection implements Collection
{
    /**
     * @var array<int|string, int>
     */
    private array $map = [];

    /**
     * @var list<T>
     */
    private array $elements = [];

    /**
     * @param iterable<T> $items
     */
    public function __construct(iterable $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
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
        return $this->elements === [];
    }

    public function size(): int
    {
        return count($this->elements);
    }

    /**
     * @param T $item
     */
    public function add(mixed $item): void
    {
        $hash = $item->hash();

        if (isset($this->map[$hash])) {
            $index = $this->map[$hash];
            if ($this->elements[$index]->equals($item)) {
                return;
            }
        }

        $this->elements[] = $item;
        $this->map[$hash] = array_key_last($this->elements);
    }

    /**
     * @param T $item
     */
    public function remove(mixed $item): bool
    {
        $hash = $item->hash();

        if (!isset($this->map[$hash])) {
            return false;
        }

        $index = $this->map[$hash];
        if (!$this->elements[$index]->equals($item)) {
            $found = false;
            foreach ($this->elements as $i => $element) {
                if ($element->equals($item)) {
                    $index = $i;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return false;
            }
        }

        unset($this->elements[$index]);
        unset($this->map[$hash]);

        $this->elements = array_values($this->elements);

        $this->map = [];
        foreach ($this->elements as $i => $element) {
            $this->map[$element->hash()] = $i;
        }

        return true;
    }

    public function clear(): void
    {
        $this->map = [];
        $this->elements = [];
    }

    /**
     * @param T $item
     */
    public function contains(mixed $item): bool
    {
        $hash = $item->hash();

        if (!isset($this->map[$hash])) {
            return false;
        }

        $index = $this->map[$hash];
        if ($this->elements[$index]->equals($item)) {
            return true;
        }

        foreach ($this->elements as $element) {
            if ($element->equals($item)) {
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
        return new ArrayIterator($this->elements);
    }

    /**
     * @return list<T>
     */
    public function toArray(): array
    {
        return $this->elements;
    }
}