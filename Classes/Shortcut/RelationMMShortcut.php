<?php

namespace Febis\SimpleTca\Shortcut;

use Febis\SimpleTca\Data\Field;

/**
 * @method self withAllowed($allowed = null)
 * @method self withMinitems($minitems = null)
 * @method self withMaxitems($maxitems = null)
 * @method self withSize($size = null)
 * @method self withMM($mM = null)
 * @method self withMMOppositeField($mMOppositeField = null)
 */
class RelationMMShortcut extends AbstractShortcut
{
    protected static function getType(): string
    {
        return "group";
    }

    protected static function getAllowedProperties(): array
    {
        return ['allowed', 'minitems', 'maxitems', 'size', 'MM', 'MM_opposite_field'];
    }

    protected static function getDefaultProperties(): array
    {
        return [
            'size' => 1
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
        protected ?string $allowed = null,
        protected ?string $mM = null,
        protected ?string $mMOppositeField = null,
        protected ?int $size = null,
        protected ?int $minitems = null,
        protected ?int $maxitems = null
    ) {
        parent::__construct($label);
    }

    protected function buildConfig(): array
    {
        $config = parent::buildConfig();
        $config['foreign_table'] = $this->allowed;

        return $config;
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
