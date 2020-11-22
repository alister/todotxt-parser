<?php

declare(strict_types=1);

namespace Alister\Test\Todotxt\Parser;

use Alister\Todotxt\Parser\TodoCounting;
use Alister\Todotxt\Parser\TodoItem;
use PHPUnit\Framework\TestCase;

class TodoCountingTest extends TestCase
{
    public function testCollectTopN(): void
    {
        $todoCounting = new TodoCounting();

        $tags = [
            'tag-a' => 10,
            'tag-b' => 20,
            'tag-c' => 30,
            'tag-d' => 25,
            'tag-e' => 10,
        ];
        $actual = $todoCounting->collectTopN($tags, 3);

        $expected = [
            ['tag name' => 'tag-c', 'count' => 30,],
            ['tag name' => 'tag-d', 'count' => 25,],
            ['tag name' => 'tag-b', 'count' => 20,],
        ];

        $this->assertSame($expected, $actual);
    }

    public function testAddCounts(): void
    {
        $tItem = new TodoItem('+project @context +project @context hello');
        $count = new TodoCounting();

        $count->addCountByUniqueContext($tItem);
        $this->assertSame(['context' => 1], $count->getContextTagsCounts());

        $count->addCountByUniqueTags($tItem);
        $this->assertSame(['project' => 1], $count->getProjectTagsCount());
    }
}
