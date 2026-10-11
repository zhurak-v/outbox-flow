<?php

namespace OutboxFlow\Core\Models\Exception\NotFound;

use OutboxFlow\Core\Models\Exception\AppException;

final class NotFoundException extends AppException
{

    private static string $ERROR_CODE = "NOT_FOUND";

    public function __construct(string $message) {
        parent::__construct($message, self::$ERROR_CODE);
    }

}