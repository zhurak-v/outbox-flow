<?php

declare(strict_types=1);

namespace OutboxFlow\Core\Models\Auditable;

use DateTimeImmutable;

interface Auditable
{

    public function getCreatedAt(): DateTimeImmutable;

    public function getUpdatedAt(): ?DateTimeImmutable;

    public function setUpdatedAt(DateTimeImmutable $updatedAt): DateTimeImmutable;

}