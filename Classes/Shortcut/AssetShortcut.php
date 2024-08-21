<?php

namespace Febis\SimpleTca\Shortcut;

use Febis\SimpleTca\Data\Field;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * @method self withMinitems($minitems = null)
 * @method self withMaxitems($maxitems = null)
 */
class AssetShortcut extends AbstractShortcut
{
    protected static function getType(): string
    {
        return "inline";
    }

    protected static function getAllowedProperties(): array
    {
        return ['minitems', 'maxitems', 'overrideChildTca', 'appearance'];
    }

    protected static function getDefaultProperties(): array
    {
        return [
            'appearance' => [
                'collapseAll' => true,
            ],
        ];
    }

    protected static function getSqlDefinition(): Field
    {
        return new Field(
            'INT',
            0,
        );
    }

    public function __construct(
        ?string $identifier = null,
        protected ?int $minitems = null,
        protected ?int $maxitems = null,
        protected ?string $fieldName = null,
        protected ?string $allowedFileExtensions = null,
    ) {
        $this->withFieldName($this->fieldName);
        parent::__construct($identifier);
    }

    public function withItemsRange(int $minitems = null, int $maxitems = null): static
    {
        $this->unsetAttributes['minitems'] = null === $minitems;
        $this->unsetAttributes['maxitems'] = null === $maxitems;

        $this->minitems = $minitems;
        $this->maxitems = $maxitems;

        return $this;
    }

    public function withFieldName($fieldName = null): static
    {
        $this->fieldName = $fieldName ?? 'assets';
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
            $this->allowedFileExtensions ?? $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext'],
        );
    }
}
