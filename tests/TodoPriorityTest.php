<?php

declare(strict_types=1);

namespace Alister\Test\Todotxt\Parser;

use Alister\Todotxt\Parser\Exceptions\UnknownPriorityValue;
use Alister\Todotxt\Parser\TodoPriority;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(TodoPriority::class)]
final class TodoPriorityTest extends TestCase
{
    /**
     * @throws UnknownPriorityValue
     */
    #[DataProvider('dpPriorityGood')]
    public function testPriorityGood(?string $priority, string $expectedPriority): void
    {
        $todoPriority = new TodoPriority($priority);
        $this->assertSame($expectedPriority, $todoPriority->getPriority());
    }

    public static function dpPriorityGood(): Generator
    {
        yield "'':''" => ['', ''];
        yield "'()':''" => ['()', ''];

        foreach ([...range('a', 'z'), ...range('A', 'Z')] as $p) {
            yield sprintf('(%s):%s', $p, $p) => [sprintf('(%s)', $p), strtoupper($p)];
            yield sprintf('good, without brackets %s:%s', $p, $p) => [$p, strtoupper($p)];
        }
    }

    #[DataProvider('dpPriorityBad')]
    public function testPriorityBad(?string $priority): void
    {
        $this->expectException(UnknownPriorityValue::class);

        new TodoPriority($priority);
    }

    public static function dpPriorityBad(): Generator
    {
        foreach (['(0)', '(1)', '(9)', '(99)', '(!)', '( )', '(#)', '(aa)', '(bad)'] as $p) {
            yield sprintf("bad: '%s'", $p) => [$p];
        }
    }
}
