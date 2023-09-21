<?php

declare(strict_types=1);

namespace Febis\SimpleTca\EventListener;

use Febis\SimpleTca\Exception\TsConfigExistsException;
use Febis\SimpleTca\TcaGenerator;
use TYPO3\CMS\Core\Configuration\Event\ModifyLoadedPageTsConfigEvent as LegacyModifyLoadedPageTsConfigEvent;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\ModifyLoadedPageTsConfigEvent;

/**
 * Class FcePageTsConfig
 * @package Febis\SimpleTca\EventListener
 */
class FcePageTsConfig
{
    /**
     * @throws TsConfigExistsException
     */
    public function __invoke(LegacyModifyLoadedPageTsConfigEvent | ModifyLoadedPageTsConfigEvent $event)
    {
        $event->addTsConfig(TcaGenerator::getTsConfigData()->getFullTsConfig());
    }
}
