<?php

namespace OutboxFlow\Core\Models\Hashable;

interface Hashable
{

    public function hash(): int;

}