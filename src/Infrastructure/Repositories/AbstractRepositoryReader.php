<?php

declare(strict_types=1);

namespace OutboxFlow\Infrastructure\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use OutboxFlow\Core\Models\Collection\ArrayCollection;
use OutboxFlow\Core\Models\Collection\Collection;
use OutboxFlow\Core\Models\Entity\Entity;
use OutboxFlow\Core\Models\Equatable\Equatable;
use OutboxFlow\Core\Models\Hashable\Hashable;
use OutboxFlow\Core\Models\Pageable\Pageable;
use OutboxFlow\Core\Repositories\RepositoryReader;

/**
 * @template ID of Hashable & Equatable
 * @template E of Entity<ID>
 *
 * @extends AbstractRepository<ID, E>
 * @implements RepositoryReader<ID, E>
 */
abstract readonly class AbstractRepositoryReader
    extends AbstractRepository
    implements RepositoryReader
{

    /**
     * @param class-string<ID> $idClass
     * @param class-string<E> $entityClass
     */
    protected function __construct(
        string $idClass,
        string $entityClass,
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct(
            $idClass,
            $entityClass,
            $entityManager
        );
    }

    /**
     * @param ID $id
     * @return E|null
     *
     * @throws ORMException
     */
    public final function findById(mixed $id): ?Entity
    {
        /** @var E|null $entity */
        $entity = $this->getEntityManager()->find(
            $this->getEntityClass(),
            $id,
        );

        return $entity;
    }

    /**
     * @param ID $id
     *
     * @throws ORMException
     */
    public final function existsById(mixed $id): bool
    {
        return $this->findById($id) !== null;
    }

    /** @return Collection<E> */
    public final function findAll(Pageable $pageable): Collection
    {
        $entities = $this->getEntityManager()
            ->getRepository($this->getEntityClass())
            ->findBy(
                criteria: [],
                orderBy: [],
                limit: $pageable->getLimit(),
                offset: $pageable->getOffset(),
            );

        return ArrayCollection::from($entities);
    }

    public final function count(): int
    {
        return $this->getEntityManager()
            ->getRepository($this->getEntityClass())
            ->count();
    }

}