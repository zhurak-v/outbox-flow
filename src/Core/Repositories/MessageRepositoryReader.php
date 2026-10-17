<?php

namespace OutboxFlow\Core\Repositories;

use OutboxFlow\Core\Models\Collection\Collection;
use OutboxFlow\Core\Models\Buffer\Buffer;
use OutboxFlow\Core\Models\Entity\MessageEntity;
use OutboxFlow\Core\Models\Entity\MessageStatus;
use OutboxFlow\Core\Models\Pageable\Pageable;

/**
 * @extends RepositoryReader<Buffer, MessageEntity>
 */
interface MessageRepositoryReader extends RepositoryReader
{

    /** @return Collection<MessageEntity> */
    public function findByTypeAndStatus(
        MessageStatus $status,
        Pageable $pageable
    ): Collection;

}