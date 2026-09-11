<?php

declare(strict_types=1);

namespace Febis\SimpleTca\Shortcut;

/**
 * @method self withAllowedTypes($allowedTypes = null)
 */
class LinkShortcut extends AbstractShortcut
{
    public function __construct(
        ?string $identifier = null,
    ) {
        parent::__construct($identifier);
    }
    #[\Override]
    protected static function getType(): string
    {
        return 'link';
    }

    #[\Override]
    protected static function getAllowedProperties(): array
    {
        return ['allowedTypes'];
    }

    #[\Override]
    protected static function getDefaultProperties(): array
    {
        return [];
    }
}
