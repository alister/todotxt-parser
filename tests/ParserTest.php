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

namespace Alister\Test\Todotxt\Parser;

use Alister\Todotxt\Parser\Exceptions\UnknownPriorityValue;
use Alister\Todotxt\Parser\Parser;
use Alister\Todotxt\Parser\TodoItem;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    /** @var string */
    private const TODO_TEXT = 'text';

    /**
     * @param string[] $tags
     * @param string[] $context
     *
     * @throws UnknownPriorityValue
     *
     * @dataProvider dpParseTodoLine
     */
    public function testParse(string $todoText, TodoItem $expectedItem, array $tags = [], array $context = []): void
    {
        $parser = new Parser();

        $this->assertEquals($expectedItem, Parser::create($todoText));

        $todoItem = $parser->parse($todoText);

        $this->assertNotNull($todoItem);
        $this->assertEquals($expectedItem, $todoItem);
        $this->assertEquals($tags, $todoItem->getTags());
        $this->assertEquals($context, $todoItem->getContext());
    }

    /**
     * @throws UnknownPriorityValue
     */
    public function dpParseTodoLine(): Generator
    {
        $created = new DateTimeImmutable('2020-01-31');
        $completion = new DateTimeImmutable('2020-02-01');
        $expected = new TodoItem(text: 'text', priority: '', created: null, completion: null, done: false);

        yield self::TODO_TEXT => [self::TODO_TEXT, $expected];

        $str = 'x text';
        $todoItem = new TodoItem(text: 'text', done: true);

        yield $str => [$str, $todoItem];

        $str = 'x (A) text';
        $todoItem = new TodoItem(text: 'text', priority: 'A', done: true);

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
