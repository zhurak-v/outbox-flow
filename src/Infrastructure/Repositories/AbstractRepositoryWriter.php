<?php

declare(strict_types=1);

namespace OutboxFlow\Infrastructure\Repositories;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use OutboxFlow\Core\Models\Collection\ArrayCollection;
use OutboxFlow\Core\Models\Collection\Collection;
use OutboxFlow\Core\Models\Entity\Entity;
use OutboxFlow\Core\Models\Equatable\Equatable;
use OutboxFlow\Core\Models\Exception\Conflict\ConflictException;
use OutboxFlow\Core\Models\Exception\NotFound\NotFoundException;
use OutboxFlow\Core\Models\Hashable\Hashable;
use OutboxFlow\Core\Repositories\RepositoryWriter;

/**
 * @template ID of Hashable & Equatable
 * @template E of Entity<ID>
 *
 * @extends AbstractRepository<ID, E>
 * @implements RepositoryWriter<ID, E>
 */
abstract readonly class AbstractRepositoryWriter
    extends AbstractRepository
    implements RepositoryWriter
{

    /**
     * @param class-string<ID> $idClass
     * @param class-string<E> $entityClass
     */
    protected function __construct(
        string $idClass,
        string $entityClass,
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct(
            $idClass,
            $entityClass,
            $entityManager,
        );
    }

    /**
     * @param E $entity
     * @return E
     *
     * @throws ConflictException
     */
    public final function save(Entity $entity): Entity
    {
        $entityManager = $this->getEntityManager();

        try {
            $entityManager->persist($entity);
            $entityManager->flush();
        } catch (UniqueConstraintViolationException $ex) {
            throw new ConflictException(
                'Conflict occurred while saving the entity.',
                0,
                $ex,
            );
        }

        return $entity;
    }

    /**
     * @param Collection<E> $entities
     * @return Collection<E>
     *
     * @throws ConflictException
     */
    public final function saveBatch(Collection $entities): Collection
    {
        $entityManager = $this->getEntityManager();

        try {
            foreach ($entities as $entity) {
                $entityManager->persist($entity);
            }

            $entityManager->flush();
        } catch (UniqueConstraintViolationException $ex) {
            throw new ConflictException(
                'Conflict occurred while saving entities.',
                0,
                $ex,
            );
        }

        return $entities;
    }

    /**
     * @param E $entity
     * @return E
     *
     * @throws ConflictException
     * @throws NotFoundException
     */
    public final function update(Entity $entity): Entity
    {
        $entityManager = $this->getEntityManager();

        if (!$entityManager->contains($entity)) {
            throw new NotFoundException('Entity is not managed.');
        }

        try {
            $entityManager->flush();
        } catch (UniqueConstraintViolationException $ex) {
            throw new ConflictException(
                'Conflict occurred while updating the entity.',
                0,
                $ex,
            );
        }

        return $entity;
    }

    /**
     * @param Collection<E> $entities
     * @return Collection<E>
     *
     * @throws ConflictException
     * @throws NotFoundException
     */
    public final function updateBatch(Collection $entities): Collection
    {
        $entityManager = $this->getEntityManager();

        foreach ($entities as $entity) {
            if (!$entityManager->contains($entity)) {
                throw new NotFoundException('Entity is not managed.');
            }
        }

        try {
            $entityManager->flush();
        } catch (UniqueConstraintViolationException $ex) {
            throw new ConflictException(
                'Conflict occurred while updating entities.',
                0,
                $ex,
            );
        }

        return $entities;
    }

    /**
     * @param ID $id
     * @return E
     *
     * @throws NotFoundException
     */
    public final function delete(mixed $id): Entity
    {
        $entityManager = $this->getEntityManager();

        /** @var E|null $entity */
        $entity = $entityManager->find(
            $this->getEntityClass(),
            $id,
        );

        if ($entity === null) {
            throw new NotFoundException('Entity not found.');
        }

        $entityManager->remove($entity);
        $entityManager->flush();

        return $entity;
    }

    /**
     * @param Collection<ID> $ids
     * @return Collection<E>
     *
     * @throws NotFoundException
     */
    public final function deleteBatch(Collection $ids): Collection
    {
        $entityManager = $this->getEntityManager();
        $entities = ArrayCollection::empty();

        foreach ($ids as $id) {
            /** @var E|null $entity */
            $entity = $entityManager->find(
                $this->getEntityClass(),
                $id,
            );

            if ($entity === null) {
                throw new NotFoundException('Some entities were not found.');
            }

            $entities->add($entity);
        }

        foreach ($entities as $entity) {
            $entityManager->remove($entity);
        }

        $entityManager->flush();

        return $entities;
    }

}