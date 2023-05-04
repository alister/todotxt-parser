<?php

declare(strict_types=1);

namespace Alister\Todotxt\Parser;

use Alister\Todotxt\Parser\Exceptions\UnknownPriorityValue;
use DateTimeInterface;
use JsonSerializable;
use Stringable;
use Symfony\Component\String\AbstractString;

use function Symfony\Component\String\s;

/**
 * @see \Alister\Test\Todotxt\Parser\TodoItemTest
 */
final class TodoItem implements Stringable, JsonSerializable
{
    private AbstractString $text;
    private readonly TodoPriority $todoPriority;

    /**
     * Holding +the +project +tags, if there are any in the text
     *
     * @var AbstractString[]
     */
    private array $tags = [];

    /**
     * Holding the @context tags, if there are any in the text
     *
     * @var AbstractString[]
     */
    private array $context = [];

    /**
     * @throws UnknownPriorityValue
     */
    public function __construct(
        string|AbstractString $text,
        string $priority = '',
        private readonly ?DateTimeInterface $created = null,
        private readonly ?DateTimeInterface $completion = null,
        private readonly bool $done = false
    ) {
        if ($text instanceof AbstractString) {
            $this->text = $text;
        } else {
            $this->text = s($text);
        }
        $this->todoPriority = new TodoPriority($priority);

        $this->parseTags($this->text);
        $this->parseContext($this->text);
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
     * phpcs:ignore Generic.Files.LineLength.TooLong
     * @return array{done: string|null, priority: string, completion: string|null, created: string|null, text: AbstractString}
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

    public function getText(): AbstractString
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
     * @return array<array-key, AbstractString>
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return array<array-key, AbstractString>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    private function parseTags(AbstractString $text): void
    {
        $this->tags = $this->collectByPrefix('+', $text);
    }

    private function parseContext(AbstractString $text): void
    {
        $this->context = $this->collectByPrefix('@', $text);
    }

    /**
     * @return array<AbstractString> prefixed word
     */
    private function collectByPrefix(string $prefixChar, AbstractString $text): array
    {
        $items = [];

        if (!$text->containsAny($prefixChar)) {
            return [];
        }

        /** @var array<AbstractString> $words */
        $words = array_unique($text->split(' '));
        /** @var AbstractString $word */
        foreach ($words as $word) {
            $word = $word->trim();
            if ($word->length() > 0 && $word->startsWith($prefixChar)) {
                $items[] = $word->trimPrefix($prefixChar);
            }
        }

        return $items;
    }
}
