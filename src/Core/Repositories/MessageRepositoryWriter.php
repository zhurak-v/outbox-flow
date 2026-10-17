<?php

namespace OutboxFlow\Core\Repositories;

use OutboxFlow\Core\Models\Buffer\Buffer;
use OutboxFlow\Core\Models\Entity\MessageEntity;

/**
 * @extends RepositoryWriter<Buffer, MessageEntity>
 */
interface MessageRepositoryWriter extends RepositoryWriter
{

}