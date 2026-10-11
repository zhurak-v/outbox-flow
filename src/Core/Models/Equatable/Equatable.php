<?php

namespace OutboxFlow\Core\Models\Equatable;

interface Equatable
{

    public function equals(object $equatable): bool;

}