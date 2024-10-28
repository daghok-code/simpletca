<?php

declare(strict_types=1);

namespace Febis\SimpleTca\EventListener;

use Febis\SimpleTca\Exception\CacheInstanceException;
use Febis\SimpleTca\Exception\TsConfigExistsException;
use Febis\SimpleTca\TcaGenerator;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\ModifyLoadedPageTsConfigEvent;

/**
 * Class FcePageTsConfig
 * @package Febis\SimpleTca\EventListener
 */
class FcePageTsConfig
{
    /**
     * @throws TsConfigExistsException
     * @throws CacheInstanceException
     */
    public function __invoke(ModifyLoadedPageTsConfigEvent $event): void
    {
        $event->addTsConfig(TcaGenerator::getTsConfigData()->getFullTsConfig());
    }
}
