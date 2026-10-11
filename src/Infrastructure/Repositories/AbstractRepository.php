<?php

declare(strict_types=1);

namespace OutboxFlow\Infrastructure\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use OutboxFlow\Core\Models\Entity\Entity;
use OutboxFlow\Core\Models\Equatable\Equatable;
use OutboxFlow\Core\Models\Hashable\Hashable;
use OutboxFlow\Core\Repositories\Repository;

/**
 * @template ID of Hashable & Equatable
 * @template E of Entity<ID>
 *
 * @implements Repository<ID, E>
 */
abstract readonly class AbstractRepository implements Repository
{

    private string $idClass;
    private string $entityClass;
    private EntityManagerInterface $entityManager;

    /**
     * @param class-string<ID> $idClass
     * @param class-string<E> $entityClass
     */
    protected function __construct(
        string $idClass,
        string $entityClass,
        EntityManagerInterface $entityManager
    ) {
        $this->idClass = $idClass;
        $this->entityClass = $entityClass;
        $this->entityManager = $entityManager;
    }

    /** @return class-string<ID> */
    public final function getIdClass(): string
    {
        return $this->idClass;
    }

    /** @return class-string<E> */
    public final function getEntityClass(): string
    {
        return $this->entityClass;
    }

    protected final function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

}