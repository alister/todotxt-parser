<?php

declare(strict_types=1);

namespace Alister\Todotxt\Parser;

use Alister\Todotxt\Parser\Exceptions\UnknownPriorityValue;
use Stringable;

/**
 * @see \Alister\Test\Todotxt\Parser\TodoPriorityTest
 */
final class TodoPriority implements Stringable
{
    /**
     * @See https://regex101.com/r/SZr0X5/13 Allows 'A'-'Z', or '(A)'-'(Z)' all with lower-case.
     * @var string
     */
    private const VALID_PRIORITIES = '#^\(?([A-Z])\)?$#i';

    private readonly string $priority;

    /**
     * @throws UnknownPriorityValue
     */
    public function __construct(?string $priority)
    {
        $this->priority = $this->getPriorityOrThrow($priority);
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * @throws UnknownPriorityValue
     */
    private function getPriorityOrThrow(?string $priority): string
    {
        if ($priority === '') {
            return '';
        }

        if ($priority === '()') {
            return '';
        }

        preg_match(self::VALID_PRIORITIES, $priority ?? '', $matches);
        if (!isset($matches[1])) {
            $displayPriority = $priority ?? 'null';

            throw UnknownPriorityValue::create($displayPriority);
        }

        return strtoupper($matches[1]);
    }

    public function __toString(): string
    {
        if ($this->priority === '') {
            return '';
        }

        return '(' . $this->priority . ')';
    }
}
