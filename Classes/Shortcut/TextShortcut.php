<?php

namespace Febis\SimpleTca\Shortcut;

/**
 * @method self withEnableRichtext(bool $enableRichtext = false)
 * @method self withRequired(bool $required = false)
 */
class TextShortcut extends AbstractShortcut
{
    #[\Override]
    protected static function getType(): string
    {
        return "text";
    }

    #[\Override]
    protected static function getAllowedProperties(): array
    {
        return ['enableRichtext', 'required'];
    }

    #[\Override]
    protected static function getDefaultProperties(): array
    {
        return [];
    }

    public function __construct(
        ?string $identifier = null,
        protected ?bool $enableRichtext = null,
        protected ?bool $required = null,
    ) {
        parent::__construct($identifier);
    }
}
