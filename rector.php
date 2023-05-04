<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        // __DIR__ . '/resources',
        // __DIR__ . '/tools',
    ]);

    // register a single rule
    // $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    $rectorConfig->skip([
        FinalizeClassesWithoutChildrenRector::class => __DIR__ . '/src/Commands/',
    ]);

    // define sets of rules
    $rectorConfig->sets([
       LevelSetList::UP_TO_PHP_82,

       SetList::ACTION_INJECTION_TO_CONSTRUCTOR_INJECTION,
       SetList::CODE_QUALITY,
       SetList::CODING_STYLE,
       // SetList::DEAD_CODE,
       SetList::NAMING,
       SetList::PRIVATIZATION,
       SetList::PSR_4,
       SetList::TYPE_DECLARATION,
       SetList::EARLY_RETURN,
       SetList::INSTANCEOF,

       SymfonyLevelSetList::UP_TO_SYMFONY_54,

       SymfonySetList::SYMFONY_CODE_QUALITY,
       SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
       SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,

       PHPUnitLevelSetList::UP_TO_PHPUNIT_90,
       PHPUnitSetList::PHPUNIT_CODE_QUALITY,
       PHPUnitSetList::PHPUNIT_EXCEPTION,
       PHPUnitSetList::REMOVE_MOCKS,
       PHPUnitSetList::PHPUNIT_SPECIFIC_METHOD,
       PHPUnitSetList::PHPUNIT_YIELD_DATA_PROVIDER,
       // PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
   ]);
};
