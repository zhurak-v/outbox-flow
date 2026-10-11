<?php

declare(strict_types=1);

namespace OutboxFlow\Core\Repositories;

use OutboxFlow\Core\Models\Entity\Entity;
use OutboxFlow\Core\Models\Equatable\Equatable;
use OutboxFlow\Core\Models\Hashable\Hashable;

/**
 * @template ID of Hashable & Equatable
 * @template E of Entity<ID>
 */
interface Repository
{

    /** @return class-string<ID> */
    public function getIdClass(): string;

    /** @return class-string<E> */
    public function getEntityClass(): string;

}