<?php

declare(strict_types=1);

namespace OutboxFlow\Core\Repositories;

use OutboxFlow\Core\Models\Collection\Collection;
use OutboxFlow\Core\Models\Entity\Entity;
use OutboxFlow\Core\Models\Equatable\Equatable;
use OutboxFlow\Core\Models\Hashable\Hashable;

/**
 * @template ID of Hashable & Equatable
 * @template E of Entity<ID>
 *
 * @extends Repository<ID, E>
 */
interface RepositoryWriter
{

    /**
     * @param E $entity
     * @return E
     */
    public function save(Entity $entity): Entity;


    /**
     * @param Collection<E> $entities
     * @return Collection<E>
     */
    public function saveBatch(Collection $entities): Collection;

    /**
     * @param E $entity
     * @return E
     */
    public function update(Entity $entity): Entity;

    /**
     * @param Collection<E> $entities
     * @return Collection<E>
     */
    public function updateBatch(Collection $entities): Collection;

    /**
     * @param ID $id
     * @return E
     */
    public function delete(mixed $id): Entity;

    /**
     * @param Collection<ID> $ids
     * @return Collection<E>
     */
    public function deleteBatch(Collection $ids): Collection;

}