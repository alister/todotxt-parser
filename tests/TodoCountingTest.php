<?php

declare(strict_types=1);

namespace Alister\Test\Todotxt\Parser;

use Alister\Todotxt\Parser\TodoCounting;
use Alister\Todotxt\Parser\TodoItem;
use PHPUnit\Framework\TestCase;

final class TodoCountingTest extends TestCase
{
    /**
     * @var array<string, int>
     */
    private const SAMPLE_TAG_COUNTS = [
        'tag-a' => 10,
        'tag-b' => 20,
        'tag-c' => 30,
        'tag-d' => 25,
        'tag-e' => 10,
    ];

    public function testCollectTopN(): void
    {
        $todoCounting = new TodoCounting();
        $actual = $todoCounting->collectTopN(self::SAMPLE_TAG_COUNTS, 3);

        $expected = [
            ['tag name' => 'tag-c', 'count' => 30,],
            ['tag name' => 'tag-d', 'count' => 25,],
            ['tag name' => 'tag-b', 'count' => 20,],
        ];

        $this->assertSame($expected, $actual);
    }

    public function testAddCounts(): void
    {
        $todoItem = new TodoItem('+project @context +project @context hello');
        $todoCounting = new TodoCounting();

        $todoCounting->addCountByUniqueContext($todoItem);
        $this->assertSame(['context' => 1], $todoCounting->getContextTagsCounts());

        $todoCounting->addCountByUniqueTags($todoItem);
        $this->assertSame(['project' => 1], $todoCounting->getProjectTagsCount());
    }
}
