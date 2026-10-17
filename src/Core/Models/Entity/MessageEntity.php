<?php

namespace OutboxFlow\Core\Models\Entity;

use Doctrine\ORM\Mapping;

use DateTimeImmutable;
use OutboxFlow\Core\Models\Buffer\Buffer;

/**
 * @extends AbstractEntity<Buffer>
 */
#[Mapping\Entity]
#[Mapping\Table(name: 'messages')]
final class MessageEntity extends AbstractEntity
{

    #[Mapping\Column(name: 'topic', type: 'string')]
    private readonly string $topic;

    #[Mapping\Column(name: 'event', type: 'string')]
    private readonly string $event;

    #[Mapping\Column(name: 'correlation_id', type: 'buffer')]
    private readonly Buffer $correlationId;

    #[Mapping\Column(name: 'status', type: 'string', enumType: MessageStatus::class)]
    private MessageStatus $status;

    #[Mapping\Column(name: 'attempts', type: 'integer')]
    private int $attempts;

    #[Mapping\Column(name: 'payload', type: 'buffer')]
    private readonly Buffer $payload;

    public function __construct(
        string $topic,
        string $event,
        Buffer $id,
        DateTimeImmutable $createdAt,
        DateTimeImmutable | null $updatedAt,
        Buffer $correlationId,
        MessageStatus $status,
        int $attempts,
        Buffer $payload
    ) {
        parent::__construct($id, $createdAt, $updatedAt);
        $this->topic = $topic;
        $this->event = $event;
        $this->correlationId = $correlationId;
        $this->status = $status;
        $this->attempts = $attempts;
        $this->payload = $payload;
    }

    public function getTopic(): string {
        return $this->topic;
    }

    public function getEvent(): string {
        return $this->event;
    }

    public function getCorrelationId(): Buffer {
        return $this->correlationId;
    }

    public function getStatus(): MessageStatus {
        return $this->status;
    }

    public function getAttempts(): int {
        return $this->attempts;
    }

    public function decrementAttempts(): self {
        $this->attempts = $this->attempts - 1;
        return $this;
    }

    public function getPayload(): Buffer {
        return $this->payload;
    }

}

enum MessageStatus: string {
    case CREATED = 'CREATED';
    case PROCESSING = 'PROCESSING';
    case SENT = 'SENT';
    case RECEIVED = 'RECEIVED';
    case PUBLISHED = 'PUBLISHED';
    case FAILED = 'FAILED';
}