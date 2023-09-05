<?php

namespace Febis\SimpleTca\FceGenerator;

use Febis\SimpleTca\Data\TsConfig\FceGroup;
use Febis\SimpleTca\Data\TsConfig\FceItem;
use Febis\SimpleTca\Exception\NoIdentifierException;
use Febis\SimpleTca\Exception\TsConfigExistsException;
use Febis\SimpleTca\FceGenerator\Showitem\Mode;
use Febis\SimpleTca\TcaGenerator;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class FceGenerator
{
    protected final const CONTENT_TABLE = 'tt_content';
    protected final const FIELDS = [
        'rowDescription' => 'rowDescription,',
        'categories' => 'categories,'
    ];
    protected final const PALETTES = [
        'general' => '--palette--;;general,',
        'headers' => '--palette--;;headers,',
        'frames' => '--palette--;;frames,',
        'appearanceLinks' => '--palette--;;appearanceLinks,',
        'hidden' => '--palette--;;hidden,',
        'access' => '--palette--;;access,',
        'language' => '--palette--;;language,',
    ];
    protected final const TABS = [
        'general' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,',
        'appearance' => '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,',
        'access' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,',
        'notes' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,',
        'extended' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,',
        'language' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,',
        'categories' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,'
    ];
    protected final const JOINED = [
        'generalPrepend' => self::TABS['general'] . self::PALETTES['general'] . self::PALETTES['headers'],
        'appearance' => self::TABS['appearance'] . self::PALETTES['frames'] . self::PALETTES['appearanceLinks'],
        'access' => self::TABS['access'] . self::PALETTES['hidden'] . self::PALETTES['access'],
        'notes' => self::TABS['notes'] . self::FIELDS['rowDescription'],
        'language' => self::TABS['language'] . self::PALETTES['language'],
        'categories' => self::TABS['categories'] . self::FIELDS['categories']
    ];

    public function __construct(
        protected string $identifier = '',
        protected string $cTypeLabel = '',
        protected string $icon = '',
        protected array $palettes = [],
        protected array $columns = [],
        protected string $showItem = '',
        protected Mode $showitemMode = Mode::Default,
        protected array $columnsOverrides = [],
        protected bool $typoscript = true,
        protected bool $tsConfig = true,
        protected string $tsConfigFceGroupIdentifier = 'default',
    ) {
        $this->cTypeLabel = $identifier . '.title';
        $this->icon = 'ce-' . $identifier;
    }

    public function withIdentifier(string $identifier = ''): static
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function withCTypeLabel(string $cTypeLabel = ''): static
    {
        $this->cTypeLabel = $cTypeLabel;
        return $this;
    }

    public function withIcon(string $icon = ''): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function withPalettes(array $palettes = []): static
    {
        $this->palettes = $palettes;
        return $this;
    }

    public function withColumns(array $columns = []): static
    {
        $this->columns = $columns;
        return $this;
    }

    public function withShowItem(string $showitem = ''): static
    {
        $this->showItem = static::withSeparatorAppended($showitem);
        return $this;
    }

    public function withShowitemMode(Mode $showitemMode = Mode::Default): static
    {
        $this->showitemMode = $showitemMode;
        return $this;
    }

    public function withColumnsOverrides(array $columnsOverrides = []): static
    {
        $this->columnsOverrides = $columnsOverrides;
        return $this;
    }

    public function withoutTyposcript(): static
    {
        $this->typoscript = false;
        return $this;
    }

    public function withoutTsConfig(): static
    {
        $this->tsConfig = false;
        return $this;
    }

    /**
     * @throws NoIdentifierException
     */
    public function registerFCE(): void
    {
        if ($this->identifier === '') {
            throw new NoIdentifierException();
        }

        ExtensionManagementUtility::addTCAcolumns(static::CONTENT_TABLE, $this->columns);
        ExtensionManagementUtility::addTcaSelectItem(
            static::CONTENT_TABLE,
            'CType',
            [
                $this->getLabel(),
                $this->identifier,
                $this->icon
            ]
        );

        $ttContentExtend = $this->buildTtContentExtend();
        ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA'], $ttContentExtend);

        if ($this->tsConfig) {
            $this->generateTsConfig();
        }

        TcaGenerator::resetItemConfig();
    }

    public function buildTtContentExtend(): array
    {
        return [
            static::CONTENT_TABLE => [
                'ctrl' => [
                    'typeicon_classes' => [
                        $this->identifier => $this->icon
                    ]
                ],
                'palettes' => $this->palettes,
                'types' => [
                    $this->identifier => [
                        'showitem' => static::generateShowitem($this->showItem, $this->showitemMode),
                        'columnsOverrides' => $this->columnsOverrides
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws TsConfigExistsException
     */
    public function generateTsConfig(): void
    {
        if (!TcaGenerator::getTsConfigData()->hasFceGroup($this->tsConfigFceGroupIdentifier)) {
            TcaGenerator::getTsConfigData()->addFceGroup(
                $this->tsConfigFceGroupIdentifier,
                new FceGroup($this->tsConfigFceGroupIdentifier)
            );
        }

        if (!TcaGenerator::getTsConfigData()->hasFceItem($this->identifier)) {
            TcaGenerator::getTsConfigData()->addFceItem(
                $this->identifier,
                new FceItem($this->identifier, $this->tsConfigFceGroupIdentifier, $this->icon, $this->getLabel())
            );
        }
    }

    /**
     * Function uses exit(0), because otherwise no output is generatedfce
     */
    public function debugRegisteringFCE(): void
    {
        $variable = [
            'ExtensionManagementUtility::addTCAcolumns' => [
                static::CONTENT_TABLE,
                $this->columns
            ],
            'ExtensionManagementUtility::addTcaSelectItem' => [
                static::CONTENT_TABLE,
                'CType',
                [
                    $this->getLabel(),
                    $this->identifier,
                    $this->icon
                ]
            ],
            'ArrayUtility::mergeRecursiveWithOverrule' => [
                $GLOBALS['TCA'],
                $this->buildTtContentExtend()
            ]
        ];
        $title = $this->identifier;
        DebuggerUtility::var_dump($variable, $title, 16);
        exit(0);
    }

    protected function getLabel(): string
    {
        if (false === empty($this->cTypeLabel) && str_starts_with($this->cTypeLabel, 'LLL:')) {
            return $this->cTypeLabel;
        } else {
            if (false === empty($this->cTypeLabel)) {
                return static::getLocalizedLabel($this->cTypeLabel);
            } else {
                return $this->identifier;
            }
        }
    }

    protected static function getLocalizedLabel(string $label): string
    {
        return TcaGenerator::getConfig()->ll() . $label;
    }

    protected static function generateShowitem(string $showitem, Mode $showitemMode): string
    {
        return match ($showitemMode) {
            Mode::Default => static::showitemDefault($showitem),
            Mode::DefaultNoHeader => static::showitemDefaultNoHeader($showitem),
            Mode::DefaultNoHeaderNoAppearance => static::showitemDefaultNoHeaderNoAppearance($showitem),
            Mode::Override => $showitem
        };
    }

    protected static function showitemDefault(string $showitem): string
    {
        return
            static::JOINED['generalPrepend'] .
            $showitem .
            static::JOINED['appearance'] .
            static::JOINED['language'] .
            static::JOINED['access'] .
            static::JOINED['categories'] .
            static::JOINED['notes'] .
            static::TABS['extended'];
    }

    protected static function showitemDefaultNoHeader(string $showitem): string
    {
        return
            self::TABS['general'] .
            self::PALETTES['general'] .
            $showitem .
            static::JOINED['appearance'] .
            static::JOINED['language'] .
            static::JOINED['access'] .
            static::JOINED['categories'] .
            static::JOINED['notes'] .
            static::TABS['extended'];
    }

    protected static function showitemDefaultNoHeaderNoAppearance(string $showitem): string
    {
        return
            self::TABS['general'] .
            self::PALETTES['general'] .
            $showitem .
            static::JOINED['language'] .
            static::JOINED['access'] .
            static::JOINED['categories'] .
            static::JOINED['notes'] .
            static::TABS['extended'];
    }

    protected static function withSeparatorAppended(string $showitem): string
    {
        return $showitem . (str_ends_with(trim($showitem), ',') ? '' : ',');
    }
}
