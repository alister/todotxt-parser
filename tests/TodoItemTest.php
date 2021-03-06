<?php

declare(strict_types=1);

namespace Alister\Test\Todotxt\Parser;

use Alister\Todotxt\Parser\Exceptions\UnknownPriorityValue;
use Alister\Todotxt\Parser\TodoItem;
use Alister\Todotxt\Parser\TodoPriority;
use DateTime;
use DateTimeInterface;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Alister\Todotxt\Parser\TodoItem
 */
class TodoItemTest extends TestCase
{
    public function testTodoItemSimplectContent(): void
    {
        $todoItem = new TodoItem('text');

        $this->assertInstanceOf(TodoItem::class, $todoItem);
        $this->assertSame($todoItem->getText(), 'text');
        $this->assertEquals($todoItem->getPriority(), new TodoPriority(''));
        $this->assertSame($todoItem->getPriority()->getPriority(), '');
        $this->assertNull($todoItem->getCreated());
        $this->assertNull($todoItem->getCompletion());
        $this->assertFalse($todoItem->isDone());
    }

    /**
     * @throws UnknownPriorityValue
     */
    public function testTodoItemDoneOnDate(): void
    {
        $created = new DateTime('2020-12-31');
        $completion = new DateTime('2021-01-15');
        $todoItem = new TodoItem('text', 'A', $created, $completion, true);

        $this->assertInstanceOf(TodoItem::class, $todoItem);
        $this->assertSame($todoItem->getText(), 'text');
        $this->assertEquals($todoItem->getPriority(), new TodoPriority('A'));
        $this->assertSame($todoItem->getPriority()->getPriority(), 'A');
        $this->assertSame($todoItem->getCreated(), $created);
        $this->assertSame($todoItem->getCompletion(), $completion);
        $this->assertTrue($todoItem->isDone());
    }

    /**
     * @throws UnknownPriorityValue
     */
    public function testTodoItemNotDone(): void
    {
        $created = new DateTime('2020-12-31');
        $todoItem = new TodoItem('text', 'A', $created, null, false);

        $this->assertInstanceOf(TodoItem::class, $todoItem);
        $this->assertSame($todoItem->getText(), 'text');
        $this->assertEquals($todoItem->getPriority(), new TodoPriority('A'));
        $this->assertSame($todoItem->getPriority()->getPriority(), 'A');
        $this->assertSame($todoItem->getCreated(), $created);
        $this->assertNull($todoItem->getCompletion());
        $this->assertFalse($todoItem->isDone());
    }

    /**
     * @throws UnknownPriorityValue
     */
    public function testTodoItemProjectTagsUniquePerTag(): void
    {
        $text = 'text +tag @context +tag @context +tag @context +tag @context';
        $todoItem = new TodoItem($text);

        $this->assertInstanceOf(TodoItem::class, $todoItem);
        $this->assertSame($todoItem->getText(), $text);

        $this->assertCount(1, $todoItem->getTags());
        $this->assertEquals(['tag'], $todoItem->getTags());

        $this->assertCount(1, $todoItem->getContext());
        $this->assertEquals(['context'], $todoItem->getContext());
    }

    /**
     * @throws UnknownPriorityValue
     *
     * @dataProvider dpTodoItemGood
     */
    public function testTodoItemGood(
        string $text,
        string $priority = '',
        ?DateTimeInterface $created = null,
        bool $done = false,
        ?DateTimeInterface $completion = null
    ): void {
        $sut = new TodoItem($text, $priority, $created, $completion, $done);
        $this->assertInstanceOf(TodoItem::class, $sut);
    }

    public function dpTodoItemGood(): Generator
    {
        $created = new DateTime('2020-12-31');
        $completion = new DateTime('2021-01-15');

        yield 'text' => ['text'];
        yield '(A) text' => ['text', 'A'];
        yield '(A) 2020-12-31 text' => ['text', 'A', $created];
        yield 'x (A) 2020-12-31 text' => ['text', 'A', $created, true];
        yield 'x (A) 2020-12-31 text +tag' => ['text +tag', 'A', $created, true];
        yield 'x (A) 2020-12-31 text +project @context' => ['text +project @context', 'A', $created, true];
        yield 'x (A) 2021-01-15 2020-12-31 text' => ['text', 'A', $created, true, $completion];
    }
}
