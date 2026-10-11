<?php

namespace OutboxFlow\Core\Models\Exception;

use Exception;

abstract class AppException extends Exception
{

    private readonly string $errorCode;

    protected function __construct(string $message, string $errorCode) {
        parent::__construct($message);
        $this->errorCode = $errorCode;
    }

    public function getErrorCode(): string {
        return $this->errorCode;
    }

}