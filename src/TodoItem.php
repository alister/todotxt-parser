<?php

declare(strict_types=1);

namespace Alister\Todotxt\Parser;

use Alister\Todotxt\Parser\Exceptions\UnknownPriorityValue;
use DateTimeInterface;

class TodoItem
{
    private string $text;
    private TodoPriority $priority;
    private ?DateTimeInterface $created;
    private ?DateTimeInterface $completion;
    private bool $done;
    /**
     * Holding +the +project +tags, if there are any in the text
     *
     * @var string[]
     */
    private array $tags;
    /**
     * Holding the @context tags, if there are any in the text
     *
     * @var string[]
     */
    private array $context;

    /**
     * @throws UnknownPriorityValue
     */
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

        $this->parseTags($text);
        $this->parseContext($text);
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

    /**
     * @return array<array-key, string>
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return array<array-key, string>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    private function parseTags(string $text): void
    {
        $this->tags = $this->collectByPrefix('+', $text);
    }

    private function parseContext(string $text): void
    {
        $this->context = $this->collectByPrefix('@', $text);
    }

    /**
     * @return string[] prefixed word
     */
    private function collectByPrefix(string $prefixChar, string $text): array
    {
        $items = [];

        if (!str_contains($text, $prefixChar)) {
            return [];
        }

        $words = array_unique(explode(' ', $text));
        foreach ($words as $word) {
            $word = trim($word);
            if (isset($word[0]) && $word[0] === $prefixChar) {
                $items[] = trim($word, $prefixChar);
            }
        }

        return $items;
    }
}
