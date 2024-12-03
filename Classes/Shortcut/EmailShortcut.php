<?php

namespace Febis\SimpleTca\Shortcut;

use Febis\SimpleTca\Data\Field;

/**
 * @method self withEval($eval = null)
 * @method self withPlaceholder($placeholder = null)
 * @method self withRequired(bool $required = false)
 */
class EmailShortcut extends AbstractShortcut
{
    #[\Override]
    protected static function getType(): string
    {
        return "email";
    }

    #[\Override]
    protected static function getAllowedProperties(): array
    {
        return ['eval', 'placeholder', 'required'];
    }

    protected static function getDefaultProperties(): array
    {
        return [];
    }

    public function __construct(
        ?string $identifier = null,
        protected ?string $eval = null,
        protected ?string $renderType = null,
        protected ?bool $required = null,
        protected ?string $placeholder = null,
    ) {
        parent::__construct($identifier);
    }
}
