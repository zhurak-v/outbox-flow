<?php

namespace OutboxFlow\Core\Models\Entity;

use DateTimeImmutable;
use OutboxFlow\Core\Models\Uuid\Uuid;

/**
 * @extends AbstractEntity<Uuid>
 */
final class MessageEntity extends AbstractEntity
{

    private readonly string $topic;

    private readonly string $event;

    private readonly Uuid $correlationId;

    private readonly MessageType $type;

    private MessageStatus $status;

    private int $attempts;

    private readonly string $payload;

    public function __construct(
        string $topic,
        string $event,
        Uuid $id,
        DateTimeImmutable $createdAt,
        DateTimeImmutable | null $updatedAt,
        Uuid $correlationId,
        MessageType $type,
        MessageStatus $status,
        int $attempts,
        string $payload
    ) {
        parent::__construct($id, $createdAt, $updatedAt);
        $this->topic = $topic;
        $this->event = $event;
        $this->correlationId = $correlationId;
        $this->type = $type;
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

    public function getCorrelationId(): Uuid {
        return $this->correlationId;
    }

    public function getType(): MessageType {
        return $this->type;
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

    public function getPayload(): string {
        return $this->payload;
    }

}

enum MessageType: string {
    case EVENT = 'EVENT';
    case COMMAND = 'COMMAND';
}

enum MessageStatus: string {
    case PENDING = 'PENDING';
    case PROCESSING = 'PROCESSING';
    case SENT = 'SENT';
    case FAILED = 'FAILED';
}