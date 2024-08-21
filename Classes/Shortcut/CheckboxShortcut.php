<?php

namespace Febis\SimpleTca\Shortcut;

use Febis\SimpleTca\Data\Field;

/**
 * @method self withRenderType($renderType = null)
 */
class CheckboxShortcut extends AbstractShortcut
{
    protected static function getType(): string
    {
        return "check";
    }

    protected static function getAllowedProperties(): array
    {
        return ['renderType'];
    }

    protected static function getDefaultProperties(): array
    {
        return [];
    }

    protected static function getSqlDefinition(): Field
    {
        return new Field(
            'TINYINT',
            0,
        );
    }

    public function __construct(
        ?string $identifier = null,
        protected ?string $renderType = null,
    ) {
        parent::__construct($identifier);
    }

    public function asToggle(): static
    {
        $this->renderType = 'checkboxToggle';
        return $this;
    }
}
