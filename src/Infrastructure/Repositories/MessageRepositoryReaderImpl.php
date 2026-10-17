<?php

namespace OutboxFlow\Infrastructure\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use OutboxFlow\Core\Models\Buffer\Buffer;
use OutboxFlow\Core\Models\Collection\ArrayCollection;
use OutboxFlow\Core\Models\Collection\Collection;
use OutboxFlow\Core\Models\Entity\MessageEntity;
use OutboxFlow\Core\Models\Entity\MessageStatus;
use OutboxFlow\Core\Models\Pageable\Pageable;
use OutboxFlow\Core\Repositories\MessageRepositoryReader;

final readonly class MessageRepositoryReaderImpl
    extends AbstractRepositoryReader
    implements MessageRepositoryReader
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct(
            Buffer::class,
            MessageEntity::class,
            $entityManager
        );
    }

    /** @return Collection<MessageEntity> */
    public function findByTypeAndStatus(
        MessageStatus $status,
        Pageable $pageable
    ): Collection
    {
        $messages = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('m')
            ->from(MessageEntity::class, 'm')
            ->where('m.type = :type')
            ->andWhere('m.status = :status')
            ->setParameter('status', $status->value)
            ->orderBy('m.createdAt', 'ASC')
            ->setFirstResult($pageable->getOffset())
            ->setMaxResults($pageable->getLimit())
            ->getQuery()
            ->getResult();

        return ArrayCollection::from($messages);
    }
}