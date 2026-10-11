<?php

declare(strict_types=1);

namespace OutboxFlow\Core\Repositories;

use OutboxFlow\Core\Models\Collection\Collection;
use OutboxFlow\Core\Models\Entity\Entity;
use OutboxFlow\Core\Models\Equatable\Equatable;
use OutboxFlow\Core\Models\Hashable\Hashable;
use OutboxFlow\Core\Models\Pageable\Pageable;

/**
 * @template ID of Hashable & Equatable
 * @template E of Entity<ID>
 *
 * @extends Repository<ID, E>
 */
interface RepositoryReader extends Repository
{

    /**
     * @param ID $id
     * @return E|null
     */
    public function findById(mixed $id): ?Entity;

    /**
     * @param ID $id
     */
    public function existsById(mixed $id): bool;


    /** @return Collection<E> */
    public function findAll(Pageable $pageable): Collection;

    public function count(): int;

}