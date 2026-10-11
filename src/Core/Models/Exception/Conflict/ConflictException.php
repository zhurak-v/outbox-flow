<?php

namespace OutboxFlow\Core\Models\Exception\Conflict;

use OutboxFlow\Core\Models\Exception\AppException;

final class ConflictException extends AppException
{

    private static string $ERROR_CODE = "CONFLICT";

    public function __construct(string $message) {
        parent::__construct($message, self::$ERROR_CODE);
    }

}