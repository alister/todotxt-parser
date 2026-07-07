<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/resources',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withComposerBased(
        twig: \true,
        doctrine: \false,
        phpunit: \true,
        symfony: \true,
    )
    // uncomment to reach your current PHP version
    ->withPhpSets(
        php84: \false,
        // php85: \false,
        // php86: \false,
    )
    ->withAttributesSets(all: \true)
    ->withSkip([
    ])
    // ->withTypeCoverageLevel(0)
    // ->withDeadCodeLevel(0)
    // ->withCodeQualityLevel(0)
;
