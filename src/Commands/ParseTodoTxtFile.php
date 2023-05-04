<?php

/** @noinspection PhpMissingFieldTypeInspection */

declare(strict_types=1);

namespace Alister\Todotxt\Parser\Commands;

use Alister\Todotxt\Parser\Exceptions\UnknownPriorityValue;
use Alister\Todotxt\Parser\Parser;
use Alister\Todotxt\Parser\TodoCounting;
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
    /** @var ?string */
    protected static $defaultName = 'app:parse-todotxt';

    private readonly TodoCounting $todoCounting;

    public function __construct()
    {
        parent::__construct();
        $this->todoCounting = new TodoCounting();
    }

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

        /** @var TodoItem $generator */
        foreach ($this->getTodoItem($filename) as $generator) {
            $this->todoCounting->addCountByUniqueTags($generator);
            $this->todoCounting->addCountByUniqueContext($generator);
        }

        $this->showTopTen($this->todoCounting->getProjectTagsCount(), 'Project tags', 10, $output);
        $this->showTopTen($this->todoCounting->getContextTagsCounts(), 'Context tags', 10, $output);

        return Command::SUCCESS;
    }

    /**
     * @param array<string,int> $tags
     */
    private function showTopTen(array $tags, string $tagName, int $maxCount, OutputInterface $output): void
    {
        $table = new Table($output);
        $table->setStyle('borderless');

        $table->setHeaders([$tagName, 'Count']);
        $table->setRows($this->todoCounting->collectTopN($tags, $maxCount));
        $table->render();
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
