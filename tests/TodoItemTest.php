<?php

declare(strict_types=1);

namespace Alister\Test\Todotxt\Parser;

use Alister\Todotxt\Parser\Exceptions\UnknownPriorityValue;
use Alister\Todotxt\Parser\TodoItem;
use Alister\Todotxt\Parser\TodoPriority;
use DateTimeImmutable;
use DateTimeInterface;
use Generator;
use PHPUnit\Framework\TestCase;

use function Symfony\Component\String\s;

/**
 * @coversDefaultClass \Alister\Todotxt\Parser\TodoItem
 */
final class TodoItemTest extends TestCase
{
    /** @var string */
    private const TODO_TEXT = 'text +tag @context +tag @context +tag @context +tag @context';

    public function testTodoItemSimplectContent(): void
    {
        $todoItem = new TodoItem('text');

        $this->assertInstanceOf(TodoItem::class, $todoItem);
        $this->assertSame($todoItem->getText()->toString(), 'text');
        $this->assertEquals($todoItem->getText(), s('text'));
        $this->assertEquals($todoItem->getPriority(), new TodoPriority(''));
        $this->assertSame($todoItem->getPriority()->getPriority(), '');
        $this->assertNull($todoItem->getCreated());
        $this->assertNull($todoItem->getCompletion());
        $this->assertFalse($todoItem->isDone());

        $this->assertSame('text', (string) $todoItem);
    }

    /**
     * @throws UnknownPriorityValue
     */
    public function testTodoItemDoneOnDate(): void
    {
        $expected = 'x (A) 2021-01-15 2020-12-31 text';
        $created = new DateTimeImmutable('2020-12-31');
        $completion = new DateTimeImmutable('2021-01-15');
        $todoItem = new TodoItem('text', 'A', $created, $completion, true);

        $this->assertInstanceOf(TodoItem::class, $todoItem);
        $this->assertSame($todoItem->getText()->toString(), 'text');
        $this->assertEquals($todoItem->getText(), s('text'));
        $this->assertEquals($todoItem->getPriority(), new TodoPriority('A'));
        $this->assertSame($todoItem->getPriority()->getPriority(), 'A');
        $this->assertSame($todoItem->getCreated(), $created);
        $this->assertSame($todoItem->getCompletion(), $completion);
        $this->assertTrue($todoItem->isDone());

        $this->assertSame($expected, (string) $todoItem);
    }

    /**
     * @throws UnknownPriorityValue
     */
    public function testTodoItemNotDone(): void
    {
        $expected = '(A) 2020-12-31 text';
        $dateTimeImmutable = new DateTimeImmutable('2020-12-31');
        $todoItem = new TodoItem('text', 'A', $dateTimeImmutable, null, false);

        $this->assertInstanceOf(TodoItem::class, $todoItem);
        $this->assertSame($todoItem->getText()->toString(), 'text');
        $this->assertEquals($todoItem->getText(), s('text'));
        $this->assertEquals($todoItem->getPriority(), new TodoPriority('A'));
        $this->assertSame($todoItem->getPriority()->getPriority(), 'A');
        $this->assertSame($todoItem->getCreated(), $dateTimeImmutable);
        $this->assertNull($todoItem->getCompletion());
        $this->assertFalse($todoItem->isDone());

        $this->assertSame($expected, (string) $todoItem);
    }

    /**
     * @throws UnknownPriorityValue
     */
    public function testTodoItemProjectTagsUniquePerTag(): void
    {
        $todoItem = new TodoItem(s(self::TODO_TEXT));

        $this->assertInstanceOf(TodoItem::class, $todoItem);
        $this->assertSame($todoItem->getText()->toString(), self::TODO_TEXT);
        $this->assertEquals($todoItem->getText(), s(self::TODO_TEXT));

        $this->assertCount(1, $todoItem->getTags());
        $this->assertEquals([s('tag')], $todoItem->getTags());

        $this->assertCount(1, $todoItem->getContext());
        $this->assertEquals([s('context')], $todoItem->getContext());

        $this->assertSame(self::TODO_TEXT, (string) $todoItem);
    }

    /**
     * @throws UnknownPriorityValue
     *
     * @dataProvider dpTodoItemGood
     * @dataProvider dpTodoItemGoodExtended
     */
    public function testTodoItemGood(
        string $text,
        string $priority = '',
        ?DateTimeInterface $created = null,
        bool $done = false,
        ?DateTimeInterface $completion = null
    ): void {
        $todoItem = new TodoItem($text, $priority, $created, $completion, $done);
        $this->assertInstanceOf(TodoItem::class, $todoItem);
        $this->assertSame($this->dataName(), (string) $todoItem);
    }

    public function dpTodoItemGood(): Generator
    {
        $created = new DateTimeImmutable('2020-12-31');
        $completion = new DateTimeImmutable('2021-01-15');

        yield 'text' => ['text'];
        yield '(A) text' => ['text', 'A'];
        yield '(A) 2020-12-31 text' => ['text', 'A', $created];
        yield 'x (A) 2020-12-31 text' => ['text', 'A', $created, true];
        yield 'x (A) 2020-12-31 text +tag' => ['text +tag', 'A', $created, true];
        yield 'x (A) 2020-12-31 text +project @context' => ['text +project @context', 'A', $created, true];
        yield 'x (A) 2021-01-15 2020-12-31 text' => ['text', 'A', $created, true, $completion];
    }

    public function dpTodoItemGoodExtended(): Generator
    {
        $created = new DateTimeImmutable('2020-12-31');
        $completion = new DateTimeImmutable('2021-01-15');

        // phpcs:ignore Generic.Files.LineLength.TooLong
        yield 'x (A) 2021-01-15 2020-12-31 Prangern wir diese Männer an' => ['Prangern wir diese Männer an', 'A', $created, true, $completion];
        yield 'x (A) 2021-01-15 2020-12-31 Test spend £0.15' => ['Test spend £0.15', 'A', $created, true, $completion];
    }

    public function testWithTodoItem(): void
    {
        $created = new DateTimeImmutable('2020-12-31');
        $todoItem = new TodoItem('hello', 'C', $created, $created, false);

        // only the created date - as it's not done.
        $this->assertSame('(C) 2020-12-31 hello', (string) $todoItem);

        $updated = new DateTimeImmutable('2023-04-27');

        $todoItem2 = $todoItem->withDone(true);
        $todoItem3 = $todoItem2->withPriority('A');
        $todoItem4 = $todoItem3->withText('test');
        $todoItem5 = $todoItem4->withCreated($updated);
        $todoItem6 = $todoItem5->withCompletion($updated);

        // and now it's totally changed...
        $this->assertSame('x (A) 2023-04-27 2023-04-27 test', (string) $todoItem6);
    }
}
