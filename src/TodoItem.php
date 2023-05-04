<?php

declare(strict_types=1);

namespace Alister\Todotxt\Parser;

use Alister\Todotxt\Parser\Exceptions\UnknownPriorityValue;
use DateTimeInterface;
use JsonSerializable;
use Stringable;

/**
 * @see \Alister\Test\Todotxt\Parser\TodoItemTest
 */
final class TodoItem implements Stringable, JsonSerializable
{
    private readonly TodoPriority $todoPriority;

    /**
     * Holding +the +project +tags, if there are any in the text
     *
     * @var string[]
     */
    private array $tags = [];

    /**
     * Holding the @context tags, if there are any in the text
     *
     * @var string[]
     */
    private array $context = [];

    /**
     * @throws UnknownPriorityValue
     */
    public function __construct(
        private readonly string $text,
        string $priority = '',
        private readonly ?DateTimeInterface $created = null,
        private readonly ?DateTimeInterface $completion = null,
        private readonly bool $done = false
    ) {
        $this->todoPriority = new TodoPriority($priority);

        $this->parseTags($text);
        $this->parseContext($text);
    }

    public function __toString(): string
    {
        $arr = array_filter(
            $this->jsonSerialize(),
            static fn($x): bool => $x !== null && $x !== ''
        );

        return implode(' ', $arr);
    }

    /**
     * @return array{done: string|null, priority: mixed, completion: string|null, created: string|null, text: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'done' => $this->done ? 'x' : null,
            'priority' => (string) $this->todoPriority,
            'completion' => $this->done ? $this->completion?->format('Y-m-d') : null,
            'created' => $this->created?->format('Y-m-d'),
            'text' => $this->text,
        ];
    }

    public function withText(string $text = ''): self
    {
        return new self(
            text: $text,
            priority: $this->todoPriority->getPriority(),
            created: $this->created,
            completion: $this->completion,
            done: $this->done
        );
    }

    public function withPriority(string $priority = ''): self
    {
        return new self(
            text: $this->text,
            priority: $priority,
            created: $this->created,
            completion: $this->completion,
            done: $this->done
        );
    }

    public function withCreated(?DateTimeInterface $created = null): self
    {
        return new self(
            text: $this->text,
            priority: $this->todoPriority->getPriority(),
            created: $created,
            completion: $this->completion,
            done: $this->done
        );
    }

    public function withCompletion(?DateTimeInterface $completion = null): self
    {
        return new self(
            text: $this->text,
            priority: $this->todoPriority->getPriority(),
            created: $this->created,
            completion: $completion,
            done: $this->done
        );
    }

    public function withDone(bool $done = false): self
    {
        return new self(
            text: $this->text,
            priority: $this->todoPriority->getPriority(),
            created: $this->created,
            completion: $this->completion,
            done: $done
        );
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getPriority(): TodoPriority
    {
        return $this->todoPriority;
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
