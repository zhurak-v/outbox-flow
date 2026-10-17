<?php

namespace OutboxFlow\Infrastructure\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use OutboxFlow\Core\Models\Buffer\Buffer;
use OutboxFlow\Core\Models\Entity\MessageEntity;
use OutboxFlow\Core\Repositories\MessageRepositoryWriter;

final readonly class MessageRepositoryWriterImpl
    extends AbstractRepositoryWriter
    implements MessageRepositoryWriter
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct(
            Buffer::class,
            MessageEntity::class,
            $entityManager
        );
    }

}