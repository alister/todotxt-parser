<?php

declare(strict_types=1);

namespace Alister\Todotxt\Parser;

use Alister\Todotxt\Parser\Exceptions\UnknownPriorityValue;

class TodoPriority
{
    // @see https://regex101.com/r/SZr0X5/13 Allows 'A'-'Z', or '(A)'-'(Z)' all with lower-case.
    private const VALID_PRIORITIES = '#^\(?([A-Z])\)?$#i';

    private string $priority;

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
        if ($priority === '' || $priority === '()') {
            return '';
        }

        preg_match(self::VALID_PRIORITIES, $priority ?? '', $matches);
        if (!isset($matches[1])) {
            $displayPriority = $priority ?? 'null';

            throw new UnknownPriorityValue("Priority should only be 'A-Z', was '{$displayPriority}'");
        }

        return strtoupper($matches[1]);
    }
}
