<?php

declare(strict_types=1);

namespace Alister\Tests\Todotxt\Parser;

use Alister\Todotxt\Parser\Parser;
use Alister\Todotxt\Parser\TodoItem;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /**
     * @param string[] $tags
     * @param string[] $context
     *
     * @dataProvider dpParseTodoLine
     */
    public function testParse(string $todoText, TodoItem $expectedItem, array $tags = [], array $context = []): void
    {
        $parser = new Parser();

        $this->assertEquals($expectedItem, Parser::create($todoText));

        $todoItem = $parser->parse($todoText);
        $this->assertEquals($expectedItem, $todoItem);
        $this->assertEquals($tags, $todoItem->getTags());
        $this->assertEquals($context, $todoItem->getContext());
    }

    public function dpParseTodoLine(): Generator
    {
        $created = new DateTimeImmutable('2020-01-31');
        $completion = new DateTimeImmutable('2020-02-01');

        $text = 'text';
        $expected = new TodoItem('text', '', null, null, false);
        yield $text => [$text, $expected];

        $str = 'x text';
        $todoItem = new TodoItem('text', '', null, null, true);
        yield $str => [$str, $todoItem];

        $str = 'x (A) text';
        $todoItem = new TodoItem('text', 'A', null, null, true);
        yield $str => [$str, $todoItem];

        $str = 'x 2020-01-31 text';
        $todoItem = new TodoItem('text', '', $created, null, true);
        yield $str => [$str, $todoItem];

        $str = 'x (Z) 2020-01-31 text';
        $todoItem = new TodoItem('text', 'Z', $created, null, true);
        yield $str => [$str, $todoItem];

        $str = 'x 2020-02-01 2020-01-31 text';
        $todoItem = new TodoItem('text', '', $created, $completion, true);
        yield $str => [$str, $todoItem];

        $str = 'x (F) 2020-02-01 2020-01-31 text';
        $todoItem = new TodoItem('text', 'F', $created, $completion, true);
        yield $str => [$str, $todoItem];

        $str = 'x (F) 2020-02-01 2020-01-31 text +tag +tag2 @context1 @context2';
        $todoItem = new TodoItem('text +tag +tag2 @context1 @context2', 'F', $created, $completion, true);
        yield $str => [$str, $todoItem, ['tag','tag2'], ['context1','context2']];
    }
}
