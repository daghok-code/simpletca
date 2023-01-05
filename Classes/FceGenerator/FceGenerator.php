<?php

namespace Febis\SimpleTca\FceGenerator;

use Febis\SimpleTca\FceGenerator\Showitem\Mode;
use Febis\SimpleTca\TcaGenerator;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class FceGenerator
{
    protected final const CONTENT_TABLE = 'tt_content';
    protected final const FIELDS = [
        'rowDescription' => 'rowDescription,'
    ];
    protected final const PALETTES = [
        'general' => '--palette--;;general,',
        'headers' => '--palette--;;headers,',
        'frames' => '--palette--;;frames,',
        'hidden' => '--palette--;;hidden,',
        'access' => '--palette--;;access,',
    ];
    protected final const TABS = [
        'general' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,',
        'appearance' => '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,',
        'access' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,',
        'notes' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,',
        'extended' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,'
    ];
    protected final const JOINED = [
        'generalPrepend' => self::TABS['general'] . self::PALETTES['general'] . self::PALETTES['headers'],
        'appearance' => self::TABS['appearance'] . self::PALETTES['frames'],
        'access' => self::TABS['access'] . self::PALETTES['hidden'] . self::PALETTES['access'],
        'notes' => self::TABS['notes'] . self::FIELDS['rowDescription'],
    ];

    public static function registerFCE(
        string $identifier,
        string $cTypeLabel = '',
        string $icon = '',
        array $palettes = [],
        array $columns = [],
        string $showItem = '',
        Mode $showitemMode = Mode::Default,
        array $columnsOverride = []
    ): void {
        ExtensionManagementUtility::addTCAcolumns(static::CONTENT_TABLE, $columns);
        ExtensionManagementUtility::addTcaSelectItem(
            static::CONTENT_TABLE,
            'CType',
            [
                false === empty($cTypeLabel) ? static::getLocalizedLabel($cTypeLabel) : $identifier,
                $identifier,
                $icon
            ]
        );

        $ttContentExtend = [
            static::CONTENT_TABLE => [
                'ctrl' => [
                    'typeicon_classes' => [
                        $identifier => $icon
                    ]
                ],
                'palettes' => $palettes,
                'types' => [
                    $identifier => [
                        'showitem' => static::generateShowitem($showItem, $showitemMode),
                        'columnsOverride' => $columnsOverride
                    ],
                ],
            ],
        ];
        ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA'], $ttContentExtend);
    }

    protected static function getLocalizedLabel(string $label): string
    {
        return TcaGenerator::getConfig()->ll() . $label;
    }

    protected static function generateShowitem(string $showitem, Mode $showitemMode): string
    {
        return match ($showitemMode) {
            Mode::Default => static::showitemDefault($showitem),
            Mode::Override => $showitem
        };
    }

    protected static function showItemDefault(string $showitem): string
    {
        return
            static::JOINED['generalPrepend'] .
            $showitem .
            static::JOINED['appearance'] .
            static::JOINED['access'] .
            static::JOINED['notes'] .
            static::TABS['extended'];
    }
}
