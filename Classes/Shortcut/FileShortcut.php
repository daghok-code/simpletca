<?php

namespace Febis\SimpleTca\Shortcut;

use Febis\SimpleTca\Data\Field;
use TYPO3\CMS\Frontend\DataProcessing\FilesProcessor;

/**
 * @method self withMinitems($minitems = null)
 * @method self withMaxitems($maxitems = null)
 */
class FileShortcut extends AbstractShortcut implements DataProcessorInterface
{
    protected static function getType(): string
    {
        return "file";
    }

    protected static function getAllowedProperties(): array
    {
        return ['minitems', 'maxitems', 'overrideChildTca', 'allowed'];
    }

    protected static function getDefaultProperties(): array
    {
        return [
            'allowed' => 'common-image-types',
        ];
    }

    protected static function getSqlDefinition(): Field
    {
        return new Field(
            'INT',
            0,
        );
    }

    public function getDataProcessorType(): string
    {
        return FilesProcessor::class;
    }

    public function getDataProcessorConfig(string $fieldName): array
    {
        return [
            'references' => [
                'table' => 'tt_content',
                'fieldName' => $fieldName,
            ],
            'as' => $fieldName
        ];
    }

    public function __construct(
        ?string $identifier = null,
        protected ?int $minitems = null,
        protected ?int $maxitems = null,
        protected ?string $allowed = null,
    ) {
        parent::__construct($identifier);
    }
}
