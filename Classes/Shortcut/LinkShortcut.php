<?php

namespace Febis\SimpleTca\Shortcut;

use Febis\SimpleTca\Data\Field;

/**
 * @method self withAllowedTypes($allowedTypes = null)
 */
class LinkShortcut extends AbstractShortcut
{
    protected static function getType(): string
    {
        return "link";
    }

    protected static function getAllowedProperties(): array
    {
        return ['allowedTypes'];
    }

    protected static function getDefaultProperties(): array
    {
        return [];
    }

    protected static function getSqlDefinition(): Field
    {
        return new Field(
            'VARCHAR(255)',
            '',
        );
    }

    public function __construct(
        ?string $identifier = null,
    ) {
        parent::__construct($identifier);
    }
}
