<?php

namespace Febis\SimpleTca\Shortcut;

use Febis\SimpleTca\Data\Field;

/**
 * @method self withForeignTable($foreignTable = null)
 * @method self withMinitems($minitems = null)
 * @method self withMaxitems($maxitems = null)
 */
class IRREShortcut extends AbstractShortcut
{
    protected static function getType(): string
    {
        return "inline";
    }

    protected static function getAllowedProperties(): array
    {
        return ['foreign_table', 'minitems', 'maxitems'];
    }

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

    protected static function getSqlDefinition(): Field
    {
        return new Field(
            'INT',
            0
        );
    }

    public function __construct(
        ?string $label = null,
        protected ?string $foreignTable = null,
        protected ?int $minitems = null,
        protected ?int $maxitems = null
    ) {
        parent::__construct($label);
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
