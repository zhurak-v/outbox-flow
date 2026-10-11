<?php

declare(strict_types=1);

namespace OutboxFlow\Core\Models\Comparator;

/**
 * @template T
 */
interface Comparator
{

    /**
     * @param T $a
     * @param T $b
     */
    public function compare(mixed $a, mixed $b): int;

}