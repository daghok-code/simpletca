<?php

namespace Febis\SimpleTca\Shortcut;

/**
 * @method self withFormat($format = null)
 * @method self withRange($range = [])
 * @method self withSlider($slider = [])
 * @method self withRequired(bool $required = false)
 */
class NumberShortcut extends AbstractShortcut
{
    #[\Override]
    protected static function getType(): string
    {
        return "number";
    }

    #[\Override]
    protected static function getAllowedProperties(): array
    {
        return [
            'format',
            'range',
            'slider',
            'required',
        ];
    }

    #[\Override]
    protected static function getDefaultProperties(): array
    {
        return [];
    }

    public function __construct(
        ?string $identifier = null,
        ?string $format = null,
        protected ?bool $required = null,
    ) {
        parent::__construct($identifier);
    }
}
