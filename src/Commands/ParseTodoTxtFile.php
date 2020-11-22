<?php

/** @noinspection PhpMissingFieldTypeInspection */

declare(strict_types=1);

namespace Alister\Todotxt\Parser\Commands;

use Alister\Todotxt\Parser\Exceptions\UnknownPriorityValue;
use Alister\Todotxt\Parser\Parser;
use Alister\Todotxt\Parser\TodoItem;
use Generator;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseTodoTxtFile extends Command
{
    /** @var string */
    protected static $defaultName = 'app:parse-todotxt';

    /** @var array<string, int> */
    private array $uniqTags = [];
    /** @var array<string, int> */
    private array $uniqContexts = [];

    protected function configure(): void
    {
        $this
            ->setDescription('Show information about a todotxt file')
            ->setHelp('Parse ')
            ->addArgument('filename', InputArgument::REQUIRED, 'todotxt file to parse');
    }

    /**
     * @throws UnknownPriorityValue
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $filename */
        $filename = $input->getArgument('filename');

        /** @var TodoItem $todoItem */
        foreach ($this->getTodoItem($filename) as $todoItem) {
            $this->addCountByUniqueTags($todoItem);
            $this->addCountByUniqueContext($todoItem);
        }

        $this->showTopTen($this->uniqTags, 'Project tags', 10, $output);
        $this->showTopTen($this->uniqContexts, 'Context tags', 10, $output);

        return Command::SUCCESS;
    }

    private function addCountByUniqueTags(TodoItem $todoItem): void
    {
        foreach ($todoItem->getTags() as $tag) {
            if (!isset($this->uniqTags[$tag])) {
                $this->uniqTags[$tag] = 0;
            }
            $this->uniqTags[$tag] ++;
        }
    }

    private function addCountByUniqueContext(TodoItem $todoItem): void
    {
        foreach ($todoItem->getContext() as $context) {
            if (!isset($this->uniqContexts[$context])) {
                $this->uniqContexts[$context] = 0;
            }
            $this->uniqContexts[$context] ++;
        }
    }

    /**
     * @param array<string,int> $tags
     */
    private function showTopTen(array $tags, string $tagName, int $maxCount, OutputInterface $output): void
    {
        $displayTag = $this->collectTopN($tags, $maxCount);

        $table = new Table($output);
        $table->setStyle('borderless');

        $table->setHeaders([$tagName, 'Count']);
        $table->setRows($displayTag);
        $table->render();
    }

    /**
     * @param array<string,int> $tags
     *
     * @return array<int, array<string,int|string>>
     */
    private function collectTopN(array $tags, int $maxCount): array
    {
        $i = 0;
        $displayTag = [];

        arsort($tags);
        foreach ($tags as $tag => $count) {
            ++$i;
            if ($i > $maxCount) {
                break;
            }
            $displayTag[$i] = ['tag name' => $tag, 'count' => $count];
        }

        return $displayTag;
    }

    /**
     * @throws UnknownPriorityValue
     */
    private function getTodoItem(string $filename): Generator
    {
        $parser = new Parser();

        $handle = @fopen($filename, 'rb');
        if (!$handle) {
            throw new RuntimeException('File cannot be opened');
        }

        while ($line = fgets($handle, 4096)) {
            if (trim($line) === '') {
                continue;
            }

            yield $parser->parse($line);
        }

        if (!feof($handle)) {
            echo "Error: unexpected fgets() fail\n";
        }
        fclose($handle);
    }
}
