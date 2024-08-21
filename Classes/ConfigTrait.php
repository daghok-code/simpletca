<?php

namespace Febis\SimpleTca;

use Febis\SimpleTca\Data\Config;
use Febis\SimpleTca\Exception\CallstackExtractionException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait ConfigTrait
{
    protected static ?Config $config = null;

    /**
     * @throws CallstackExtractionException
     */
    public static function getConfig(): Config
    {
        if (!static::$config instanceof Config) {
            static::$config = GeneralUtility::makeInstance(Config::class);
        }

        return static::$config->switchFileConfig();
    }
}
