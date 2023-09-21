<?php

use Febis\SimpleTca\Hook\TyposcriptLoader;
use TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') or die();

$cacheConfiguration = [
    'frontend' => PhpFrontend::class,
    'backend' => SimpleFileBackend::class,
    'options' => [
        'defaultLifetime' => 0,
    ],
    'groups' => ['system'],
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['simpletca_tsconfig'] ??= $cacheConfiguration;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['simpletca_typoscript'] ??= $cacheConfiguration;

if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() < 12) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Core/TypoScript/TemplateService']['runThroughTemplatesPostProcessing']
    [1718744765] = TyposcriptLoader::class . '->addGeneratedTypoScript';
}
