<?php

declare(strict_types=1);

namespace Febis\SimpleTca\EventListener;

use Febis\SimpleTca\Shortcut\TcaShortcutInterface;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;

/**
 * Class TcaGeneratorTableDefinitionUpdate
 * @package Febis\SimpleTca\EventListener
 */
class TcaGeneratorBuildTca
{
    public function __invoke(AfterTcaCompilationEvent $event)
    {
        $fullTca = $event->getTca();
        foreach ($fullTca as $tableName => $tca) {
            foreach ($tca['columns'] ?? [] as $columnName => $column) {
                if ($column instanceof TcaShortcutInterface) {
                    $fullTca[$tableName]['columns'][$columnName] = $column->build();
                }
            }
        }
        $event->setTca($fullTca);
    }
}
