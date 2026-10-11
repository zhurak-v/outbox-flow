<?php

namespace OutboxFlow\Core\Models\Entity;

use DateTimeImmutable;
use OutboxFlow\Core\Models\Equatable\Equatable;
use OutboxFlow\Core\Models\Hashable\Hashable;

use Doctrine\ORM\Mapping;

/**
 * @template ID of Hashable & Equatable
 *
 * @implements Entity<ID>
 */
#[Mapping\MappedSuperclass]
abstract class AbstractEntity implements Entity
{

    #[Mapping\Id]
    #[Mapping\Column(name: 'id')]
    protected readonly mixed $id;

    #[Mapping\Column(name: 'created_at', type: 'datetime_immutable')]
    protected readonly DateTimeImmutable $createdAt;

    #[Mapping\Column(name: 'updated_at', type: 'datetime_immutable', nullable: true)]
    protected ?DateTimeImmutable $updatedAt;

    protected function __construct(
        mixed $id,
        DateTimeImmutable $createdAt,
        DateTimeImmutable | null $updatedAt
    )
    {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return ID
     */
    public final function getId(): mixed {
        return $this->id;
    }

    public final function getCreatedAt(): DateTimeImmutable {
        return $this->createdAt;
    }

    public final function getUpdatedAt(): ?DateTimeImmutable {
        return $this->updatedAt;
    }

    public final function setUpdatedAt(DateTimeImmutable $updatedAt): DateTimeImmutable {
        $this->updatedAt = $updatedAt;
        return $updatedAt;
    }

    public final function hash(): int {
        return $this->id->hash();
    }

    public final function equals(object $equatable): bool {
        if ($this === $equatable) {
            return true;
        }

        if (!$equatable instanceof self) {
            return false;
        }

        return $this->id->equals($equatable->getId());

    }

}