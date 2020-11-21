<?php

declare(strict_types=1);

namespace Alister\Todotxt\Parser;

use DateTimeInterface;

class TodoItem
{
    private string $text;
    private TodoPriority $priority;
    private ?DateTimeInterface $created;
    private ?DateTimeInterface $completion;
    private bool $done;

    public function __construct(
        string $text,
        string $priority = '',
        ?DateTimeInterface $created = null,
        ?DateTimeInterface $completion = null,
        bool $done = false
    ) {
        $this->priority = new TodoPriority($priority);
        $this->created = $created;
        $this->text = $text;
        $this->completion = $completion;
        $this->done = $done;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getPriority(): TodoPriority
    {
        return $this->priority;
    }

    public function getCreated(): ?DateTimeInterface
    {
        return $this->created;
    }

    public function getCompletion(): ?DateTimeInterface
    {
        return $this->completion;
    }

    public function isDone(): bool
    {
        return $this->done;
    }
}
