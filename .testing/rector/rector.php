<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PostRector\Rector\NameImportingPostRector;
use Ssch\TYPO3Rector\CodeQuality\General\ConvertImplicitVariablesToExplicitGlobalsRector;
use Ssch\TYPO3Rector\CodeQuality\General\ExtEmConfRector;
use Ssch\TYPO3Rector\Configuration\Typo3Option;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;

return RectorConfig::configure()
    ->withPhpSets()
    ->withPreparedSets(deadCode: true, codeQuality: true, codingStyle: true, privatization: true)
    ->withSets(
        [
            Typo3LevelSetList::UP_TO_TYPO3_11,
        ],
    )
    ->withPHPStanConfigs([Typo3Option::PHPSTAN_FOR_RECTOR_PATH])
    ->withSkip(
        [
            __DIR__ . '/vendor/*',
            __DIR__ . '/.testing/*',
            __DIR__ . '/public/*',
            __DIR__ . '/bin/*',
            NameImportingPostRector::class => [
                'ext_localconf.php',
                'ext_tables.php',
                'ClassAliasMap.php',
            ],
        ],
    )
    ->withRules(
        [
            ConvertImplicitVariablesToExplicitGlobalsRector::class,
        ],
    )
    ->withConfiguredRule(
        ExtEmConfRector::class,
        [
            ExtEmConfRector::ADDITIONAL_VALUES_TO_BE_REMOVED => [],
        ],
    );
