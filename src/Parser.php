<?php

/**
 * This file is part of alister/todotxt-parser
 *
 * alister/todotxt-parser is open source software: you can distribute
 * it and/or modify it under the terms of the MIT License
 * (the "License"). You may not use this file except in
 * compliance with the License.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
 * implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 * @copyright Copyright (c) Alister Bulman <abulman@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Alister\Todotxt\Parser;

use Alister\Todotxt\Parser\Exceptions\UnknownPriorityValue;
use DateTimeImmutable;
use DateTimeInterface;
use Throwable;

class Parser
{
    // @see https://regex101.com/r/SZr0X5/14
    private const REGEX_PRIORITY_MATCH = '#\(([a-zA-Z])\) #';
    // @see https://regex101.com/r/SZr0X5/15 matching date-like things (yyyy-mm-dd)
    private const REGEX_DATES_MATCH = '#(?:(?:19|20)\d\d)-(?:0?[1-9]|1[012])-(?:[12][\d]|3[01]|0?[1-9])#';
    private string $todoLine = '';

    public function __construct()
    {
    }

    /**
     * @throws UnknownPriorityValue
     */
    public static function create(string $todoLine = ''): ?TodoItem
    {
        return (new self())->parse($todoLine);
    }

    /**
     * @throws UnknownPriorityValue
     */
    public function parse(string $todoLine = ''): ?TodoItem
    {
        $this->setTodo($todoLine);

        $done = $this->parseDone();
        $priority = $this->parsePriority();

        try {
                [$completion, $created] = $this->parseDates();
        } catch (Throwable $e) {
            return null;
        }

        $text = $this->todoLine;

        return new TodoItem($text, $priority, $created, $completion, $done);
    }

    private function setTodo(string $todoLine): string
    {
        $this->todoLine = trim($todoLine);

        return $this->todoLine;
    }

    private function parseDone(): bool
    {
        $isDone = str_starts_with($this->todoLine, 'x ');

        if ($isDone) {
            $this->setTodo(substr($this->todoLine, 2));   // length of 'x '
        }

        return $isDone;
    }

    private function parsePriority(): string
    {
        $hasPriority = (bool) preg_match(self::REGEX_PRIORITY_MATCH, $this->todoLine, $match);

        $priority = '';
        if ($hasPriority) {
            $priority = $match[1];
            $this->setTodo(substr($this->todoLine, 4));   // length of '(a) '
        }

        return $priority;
    }

    /**
     * @return ?DateTimeInterface[]
     *
     * @throws \Exception
     *
     * @psalm-return array<int, DateTimeInterface|null>
     */
    private function parseDates(): array
    {
        $date1 = $this->snipDateFromLine();
        $date2 = $this->snipDateFromLine();

        $completion = null;
        $created = null;
        if (isset($date1, $date2)) {
            $completion = $date1;
            $created = $date2;
        } elseif (isset($date1)) {
            $created = $date1;
        }

        return [$completion, $created];
    }

    /**
     * @throws \Exception
     */
    private function snipDateFromLine(): ?DateTimeInterface
    {
        $hasDate = (bool) preg_match(self::REGEX_DATES_MATCH, $this->todoLine, $match);
        if (!$hasDate) {
            return null;
        }

        $date = new DateTimeImmutable($match[0]);
        $this->setTodo(substr($this->todoLine, 10));   // length of 'yyyy-mm-dd'

        return $date;
    }
}
