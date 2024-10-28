<?php

declare(strict_types=1);

namespace Febis\SimpleTca\EventListener;

use Febis\SimpleTca\Shortcut\TcaShortcutInterface;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;
use TYPO3\CMS\Core\Configuration\Tca\TcaPreparation;

/**
 * Class TcaGeneratorTableDefinitionUpdate
 * @package Febis\SimpleTca\EventListener
 */
final readonly class TcaGeneratorBuildTca
{
    public function __construct(private TcaPreparation $tcaPreparation)
    {
    }

    public function __invoke(AfterTcaCompilationEvent $event): void
    {
        $fullTca = $event->getTca();

        foreach ($fullTca as $tableName => $tca) {
            foreach ($tca['columns'] ?? [] as $columnName => $column) {
                if ($column instanceof TcaShortcutInterface) {
                    $fullTca[$tableName]['columns'][$columnName] = $column->build();
                }

                if (isset($fullTca[$tableName]['columns'][$columnName]['_identifier'])) {
                    unset($fullTca[$tableName]['columns'][$columnName]['_identifier']);
                }
            }
        }

        $fullTca = $this->tcaPreparation->prepare($fullTca);

        $event->setTca($fullTca);
    }
}
