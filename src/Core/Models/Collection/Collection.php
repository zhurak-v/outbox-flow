<?php

declare(strict_types=1);

namespace OutboxFlow\Core\Models\Collection;

use IteratorAggregate;

use OutboxFlow\Core\Models\Equatable\Equatable;
use OutboxFlow\Core\Models\Hashable\Hashable;

/**
 * @template T of Hashable & Equatable
 *
 * @extends IteratorAggregate<int, T>
 */
interface Collection extends IteratorAggregate
{
    public function isEmpty(): bool;

    public function size(): int;

    /**
     * @param T $item
     */
    public function add(mixed $item): void;

    /**
     * @param T $item
     */
    public function remove(mixed $item): bool;

    public function clear(): void;

    /**
     * @param T $item
     */
    public function contains(mixed $item): bool;

    /**
     * @return list<T>
     */
    public function toArray(): array;
}