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
class TodoPriorityTest extends TestCase
{
    /**
     * @dataProvider dpPriorityGood
     */
    public function testPriorityGood(?string $priority, string $expectedPriority): void
    {
        $actual = new TodoPriority($priority);
        $this->assertSame($expectedPriority, $actual->getPriority());
    }

    public function dpPriorityGood(): Generator
    {
        yield "'':''" => ['', ''];
        yield "'()':''" => ['()', ''];

        foreach ([...range('a', 'z'), ...range('A', 'Z')] as $p) {
            yield "($p):$p" => ["($p)", strtoupper($p)];
            yield "good, without brackets $p:$p" => [$p, strtoupper($p)];
        }
    }

    /**
     * @dataProvider dpPriorityBad
     */
    public function testPriorityBad(?string $priority): void
    {
        $this->expectException(UnknownPriorityValue::class);
        $actual = new TodoPriority($priority);
    }

    public function dpPriorityBad(): Generator
    {
        foreach (['(0)', '(1)', '(9)', '(99)', '(!)', '( )', '(#)', '(aa)', '(bad)'] as $p) {
            yield "bad: '$p'" => [$p];
        }
    }
}
