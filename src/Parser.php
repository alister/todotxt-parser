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

namespace Alister\Todotxt\Parser;

class Parser
{
    public static function create(string $todoLine = ''): TodoItem
    {
        return (new self())->parse($todoLine);
    }

    public function parse(string $todoLine = ''): TodoItem
    {
        $text = '';
        $priority = '';
        $created = null;
        $completion = null;
        $done = false;

        return new TodoItem($text, $priority, $created, $completion, $done);
    }
}
