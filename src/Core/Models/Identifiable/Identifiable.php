<?php

declare(strict_types=1);

namespace OutboxFlow\Core\Models\Identifiable;

use OutboxFlow\Core\Models\Hashable\Hashable;
use OutboxFlow\Core\Models\Equatable\Equatable;

/**
 * @template ID of Hashable & Equatable
 */
interface Identifiable
{

    /**
     * @return ID
     */
    public function getId(): mixed;

}