<?php

namespace OutboxFlow\Core\Models\Buffer;

use OutboxFlow\Core\Models\Equatable\Equatable;
use OutboxFlow\Core\Models\Hashable\Hashable;

interface Buffer extends Hashable, Equatable
{

    public function getLength(): int;

    public function getCapacity(): int;

    public function getByte(int $index): int;

    public function getBytes(int $index, int $length): string;

    public function isEmpty(): bool;

}