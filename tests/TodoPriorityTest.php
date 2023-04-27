<?php

declare(strict_types=1);

namespace Alister\Test\Todotxt\Parser;

use Alister\Todotxt\Parser\Exceptions\UnknownPriorityValue;
use Alister\Todotxt\Parser\TodoPriority;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Alister\Todotxt\Parser\TodoPriority
 */
final class TodoPriorityTest extends TestCase
{
    /**
     * @throws UnknownPriorityValue
     *
     * @dataProvider dpPriorityGood
     */
    public function testPriorityGood(?string $priority, string $expectedPriority): void
    {
        $todoPriority = new TodoPriority($priority);
        $this->assertSame($expectedPriority, $todoPriority->getPriority());
    }

    public function dpPriorityGood(): Generator
    {
        yield "'':''" => ['', ''];
        yield "'()':''" => ['()', ''];

        foreach ([...range('a', 'z'), ...range('A', 'Z')] as $p) {
            yield sprintf('(%s):%s', $p, $p) => [sprintf('(%s)', $p), strtoupper($p)];
            yield sprintf('good, without brackets %s:%s', $p, $p) => [$p, strtoupper($p)];
        }
    }

    /**
     * @dataProvider dpPriorityBad
     */
    public function testPriorityBad(?string $priority): void
    {
        $this->expectException(UnknownPriorityValue::class);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $todoPriority = new TodoPriority($priority);
    }

    public function dpPriorityBad(): Generator
    {
        foreach (['(0)', '(1)', '(9)', '(99)', '(!)', '( )', '(#)', '(aa)', '(bad)'] as $p) {
            yield sprintf("bad: '%s'", $p) => [$p];
        }
    }
}
