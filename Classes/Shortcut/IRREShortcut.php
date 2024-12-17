<?php

namespace Febis\SimpleTca\Shortcut;

use TYPO3\CMS\Frontend\DataProcessing\DatabaseQueryProcessor;

/**
 * @method self withForeignTable($foreignTable = null)
 * @method self withMinitems($minitems = null)
 * @method self withMaxitems($maxitems = null)
 */
class IRREShortcut extends AbstractShortcut implements RecursiveDataProcessorInterface
{
    #[\Override]
    protected static function getType(): string
    {
        return "inline";
    }

    #[\Override]
    protected static function getAllowedProperties(): array
    {
        return [
            'foreign_table',
            'minitems',
            'maxitems',
        ];
    }

    #[\Override]
    protected static function getDefaultProperties(): array
    {
        return [
            'foreign_field' => 'parent',
            'appearance' => [
                'collapseAll' => true,
                'showSynchronizationLink' => true,
                'showAllLocalizationLink' => true,
                'showPossibleLocalizationRecords' => true,
            ],
        ];
    }

    #[\Override]
    public function getDataProcessorType(): string
    {
        return DatabaseQueryProcessor::class;
    }

    #[\Override]
    public function getDataProcessorConfig(string $fieldName, string $tableName): array
    {
        return [
            'table' => $this->foreignTable,
            'where.data' => 'field:uid',
            'where.wrap' => 'parent=|',
            'pidInList.field' => 'pid',
            'orderBy' => 'sorting',

            'as' => $fieldName,
        ];
    }

    #[\Override]
    public function getTcaTable(): string
    {
        return $this->foreignTable;
    }

    public function __construct(
        ?string $identifier = null,
        protected ?string $foreignTable = null,
        protected ?int $minitems = null,
        protected ?int $maxitems = null,
    ) {
        parent::__construct($identifier);
    }

    public function withItemsRange(int $minitems = null, int $maxitems = null): static
    {
        $this->unsetAttributes['minitems'] = null === $minitems;
        $this->unsetAttributes['maxitems'] = null === $maxitems;

        $this->minitems = $minitems;
        $this->maxitems = $maxitems;

        return $this;
    }
}
