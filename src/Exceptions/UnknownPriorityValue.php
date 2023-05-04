<?php

declare(strict_types=1);

namespace Alister\Todotxt\Parser\Exceptions;

use Exception;
use Throwable;

final class UnknownPriorityValue extends Exception
{
    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(string $displayPriority): self
    {
        return new self(sprintf("Priority should only be 'A-Z', was '%s'", $displayPriority));
    }
}
