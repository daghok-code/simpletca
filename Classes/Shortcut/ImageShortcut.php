<?php

namespace Febis\SimpleTca\Shortcut;

use Febis\SimpleTca\Data\Field;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * @method self withMinitems($minitems = null)
 * @method self withMaxitems($maxitems = null)
 * @method self withFieldname($fieldname = null)
 */
class ImageShortcut extends AbstractShortcut
{
    protected static function getType(): string
    {
        return "inline";
    }

    protected static function getAllowedProperties(): array
    {
        return ['minitems', 'maxitems'];
    }

    protected static function getDefaultProperties(): array
    {
        return [
            'appearance' => [
                'collapseAll' => true,
            ],
            'overrideChildTca' => [
                'types' => [
                    AbstractFile::FILETYPE_IMAGE => [
                        'showitem' => '
                            --palette--;;imageoverlayPalette,
                            --palette--;;filePalette
                        ',
                    ],
                ],
            ],
            'minitems' => 1,
            'maxitems' => 1,
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
        protected ?int $minitems = null,
        protected ?int $maxitems = null,
        protected ?string $fieldName = null,
        protected ?string $allowedFileExtensions = null,
    ) {
        $this->fieldName = $this->fieldName ?? 'image';
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

    public function withAllowedFileExtension($allowedFileExtensions = null): static
    {
        $this->allowedFileExtensions = $allowedFileExtensions;
        return $this;
    }

    protected function buildConfig(): array
    {
        return ExtensionManagementUtility::getFileFieldTCAConfig(
            $this->fieldName,
            parent::buildConfig(),
            $this->allowedFileExtensions ?? $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
        );
    }
}
