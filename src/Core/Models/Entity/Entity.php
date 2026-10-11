<?php

declare(strict_types=1);

namespace OutboxFlow\Core\Models\Entity;

use OutboxFlow\Core\Models\Auditable\Auditable;
use OutboxFlow\Core\Models\Equatable\Equatable;
use OutboxFlow\Core\Models\Hashable\Hashable;
use OutboxFlow\Core\Models\Identifiable\Identifiable;

/**
 * @template ID of Hashable & Equatable
 *
 * @extends Identifiable<ID>
 */
interface Entity extends Identifiable, Auditable, Hashable, Equatable {}