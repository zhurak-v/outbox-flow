<?php

declare(strict_types=1);

namespace OutboxFlow\Core\Models\Pageable;

interface Pageable
{

    public function getOffset(): int;
    public function getLimit(): int;

}