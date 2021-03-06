<?php

declare(strict_types=1);

namespace Alister\Todotxt\Parser;

class TodoCounting
{
    /** @var array<string, int> */
    private array $uniqContexts = [];
    /** @var array<string, int> */
    private array $uniqTags = [];

    /**
     * @param array<string,int> $tags
     *
     * @return array<int, array<string,int|string>>
     */
    public function collectTopN(array $tags, int $maxCount): array
    {
        $i = 0;
        $displayTag = [];

        arsort($tags);
        foreach ($tags as $tag => $count) {
            $displayTag[$i] = ['tag name' => $tag, 'count' => $count];
            ++$i;
            if ($i >= $maxCount) {
                break;
            }
        }

        return $displayTag;
    }

    public function addCountByUniqueContext(TodoItem $todoItem): void
    {
        foreach ($todoItem->getContext() as $context) {
            if (!isset($this->uniqContexts[$context])) {
                $this->uniqContexts[$context] = 0;
            }
            $this->uniqContexts[$context]++;
        }
    }

    public function addCountByUniqueTags(TodoItem $todoItem): void
    {
        foreach ($todoItem->getTags() as $tag) {
            if (!isset($this->uniqTags[$tag])) {
                $this->uniqTags[$tag] = 0;
            }
            $this->uniqTags[$tag]++;
        }
    }

    /**
     * @return array<string,int>
     */
    public function getContextTagsCounts(): array
    {
        return $this->uniqContexts;
    }

    /**
     * @return array<string,int>
     */
    public function getProjectTagsCount(): array
    {
        return $this->uniqTags;
    }
}
