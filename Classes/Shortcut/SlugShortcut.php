<?php

namespace Febis\SimpleTca\Shortcut;


/**
 * @method self withSize($size = null)
 * @method self withEval($eval = null)
 */
class SlugShortcut extends AbstractShortcut
{
    #[\Override]
    protected static function getType(): string
    {
        return "slug";
    }

    #[\Override]
    protected static function getAllowedProperties(): array
    {
        return [
            'size',
            'eval',
        ];
    }

    #[\Override]
    protected static function getDefaultProperties(): array
    {
        return [
            'size' => '80',
            'generatorOptions' => [
                'fields' => [
                    'title',
                ],
                'fieldSeparator' => '/',
                'replacements' => [
                    '/' => '-',
                ],
            ],
            'fallbackCharactor' => '-',
            'eval' => 'uniqueInSite',
            'default' => '',
        ];
    }

    public function __construct(
        ?string $identifier = null,
        protected ?string $size = null,
        protected ?string $eval = null,
    ) {
        parent::__construct($identifier);
    }
}
