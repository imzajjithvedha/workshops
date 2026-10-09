<?php

namespace App\Exceptions;

use RuntimeException;

class BusinessRuleException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status = 422)
    {
        parent::__construct($message);
    }

    public static function workshopFull(): self
    {
        return new self('This workshop is full. No seats are left.', 409);
    }
}
