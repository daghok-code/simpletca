<?php

namespace Febis\SimpleTca;

use Febis\SimpleTca\Data\Config;
use Febis\SimpleTca\Data\ItemConfig;
use Febis\SimpleTca\Data\TcaDefinitionData;
use Febis\SimpleTca\Data\TsConfig\FceGroup;
use Febis\SimpleTca\Data\TsConfig\TsConfigData;
use Febis\SimpleTca\Exception\CallstackExtractionException;
use Febis\SimpleTca\Exception\MethodNotDefinedException;
use Febis\SimpleTca\Exception\ShortcutNotAllowedException;
use Febis\SimpleTca\Exception\TsConfigExistsException;
use Febis\SimpleTca\FceGenerator\FceGenerator;
use Febis\SimpleTca\FceGenerator\Showitem\Mode;
use Febis\SimpleTca\Shortcut\AbstractShortcut;
use Febis\SimpleTca\Shortcut\AssetShortcut;
use Febis\SimpleTca\Shortcut\CheckboxShortcut;
use Febis\SimpleTca\Shortcut\ImageShortcut;
use Febis\SimpleTca\Shortcut\InputShortcut;
use Febis\SimpleTca\Shortcut\IRREShortcut;
use Febis\SimpleTca\Shortcut\LinkShortcut;
use Febis\SimpleTca\Shortcut\PassthroughShortcut;
use Febis\SimpleTca\Shortcut\RelationMMShortcut;
use Febis\SimpleTca\Shortcut\RelationShortcut;
use Febis\SimpleTca\Shortcut\RteShortcut;
use Febis\SimpleTca\Shortcut\SelectSingleShortcut;
use Febis\SimpleTca\Shortcut\SlugShortcut;
use Febis\SimpleTca\Shortcut\TcaShortcutInterface;
use Febis\SimpleTca\TcaBuilder\TcaBuilder;
use ReflectionException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @method static AssetShortcut createAsset($identifier = null, $minitems = null, $maxitems = null, $fieldname = null)
 * @method static CheckboxShortcut createCheckbox($identifier = null, $renderType = null)
 * @method static ImageShortcut createImage($identifier = null, $minitems = null, $maxitems = null, $fieldname = null)
 * @method static InputShortcut createInput($identifier = null, $eval = null, $renderType = null)
 * @method static IRREShortcut createIRRE($identifier = null, $foreignTable = null, $minitems = null, $maxitems = null)
 * @method static LinkShortcut createLink($identifier = null)
 * @method static PassthroughShortcut createPassthrough($identifier = null)
 * @method static RelationShortcut createRelation($identifier = null, $allowed = null, $size = null, $minitems = null, $maxitems = null)
 * @method static RelationMMShortcut createRelationMM($identifier = null, $allowed = null, $mM = null, $mMOppositeField = null, $size = null, $minitems = null, $maxitems = null)
 * @method static RteShortcut createRte($identifier = null)
 * @method static SelectSingleShortcut createSelectSingle($identifier = null, $items = null, $renderType = null)
 * @method static SlugShortcut createSlug($identifier = null, $size = null, $eval = null)
 */
class TcaGenerator
{
    protected static ?TcaDefinitionData $tcaDefinitionDataInstance = null;
    protected static ?TsConfigData $tsConfigData = null;
    protected static ?Config $config = null;
    protected static ?ItemConfig $tmpItemConfig = null;

    private function __construct()
    {
    }

    /**
     * @throws CallstackExtractionException
     */
    public static function createTca(string $table = null, string $extKey = null): TcaBuilder
    {
        self::getConfig()->injectRuntimeData();

        return new TcaBuilder($table ?? self::$config->getTablename());
    }

    /**
     * @param string $identifier
     * @param string $cTypeLabel
     * @param string $icon
     * @param array $palettes
     * @param array $columns
     * @param string $showItem
     * @param Mode $showitemMode
     * @param array $columnsOverrides
     * @param bool $typoscript
     * @param bool $tsConfig
     * @return FceGenerator
     */
    public static function createFCE(
        string $identifier,
        string $cTypeLabel = '',
        string $icon = '',
        array $palettes = [],
        array $columns = [],
        string $showItem = '',
        Mode $showitemMode = Mode::Default,
        array $columnsOverrides = [],
        bool $typoscript = true,
        bool $tsConfig = true,
    ): FceGenerator {
        self::getConfig()->injectRuntimeData();
        self::$tmpItemConfig = GeneralUtility::makeInstance(ItemConfig::class, $identifier);

        return GeneralUtility::makeInstance(
            FceGenerator::class,
            $identifier,
            $cTypeLabel,
            $icon,
            $palettes,
            $columns,
            $showItem,
            $showitemMode,
            $columnsOverrides,
            $typoscript,
            $tsConfig,
        );
    }

    /**
     * @throws TsConfigExistsException
     */
    private static function overrideDefaultTsConfigFceGroup(string $header, string $show = '*'): void
    {
        static::getTsConfigData()->addFceGroup('default', new FceGroup('group', $header, $show), true);
    }

    public static function getTcaDefinitionDataInstance(): TcaDefinitionData
    {
        if (static::$tcaDefinitionDataInstance === null) {
            static::$tcaDefinitionDataInstance = GeneralUtility::makeInstance(TcaDefinitionData::class);
        }
        return static::$tcaDefinitionDataInstance;
    }

    /**
     * @throws TsConfigExistsException
     */
    public static function getTsConfigData(): TsConfigData
    {
        if (static::$tsConfigData === null) {
            static::$tsConfigData = GeneralUtility::makeInstance(TsConfigData::class);

            if (!static::$tsConfigData->hasFceGroup('default')) {
                static::$tsConfigData->addFceGroup('default', new FceGroup('default', 'Default'));
            }
        }
        return static::$tsConfigData;
    }

    /**
     * @throws CallstackExtractionException
     */
    public static function getConfig(): Config
    {
        if (static::$config === null) {
            static::$config = GeneralUtility::makeInstance(Config::class);
            static::$config->injectRuntimeData();
        }
        return static::$config;
    }

    public static function getItemConfig(): ItemConfig
    {
        return static::$tmpItemConfig;
    }

    public static function resetItemConfig(): void
    {
        static::$tmpItemConfig = null;
    }

    /**
     * Calls Shortcut Methods
     * @param string $name
     * @param array $arguments
     * @return TcaShortcutInterface
     * @throws MethodNotDefinedException
     * @throws ShortcutNotAllowedException|CallstackExtractionException
     */
    public static function __callStatic(string $name, array $arguments): TcaShortcutInterface
    {
        preg_match('/\Acreate([A-Z][A-z0-9]+)\z/', $name, $match);

        if (isset($match[0], $match[1])) {
            $fqcn = self::getFQCN($match[1]);
            try {
                $reflectionClass = new \ReflectionClass($fqcn);
                $shortcutInstance = $reflectionClass->newInstance(...$arguments);
                if ($reflectionClass->implementsInterface(TcaShortcutInterface::class)) {
                    self::$config->injectRuntimeData();

                    if($shortcutInstance instanceof AbstractShortcut) {
                        $shortcutInstance->withTablename(self::$config->getTablename());
                    }

                    /** @var TcaShortcutInterface $shortcutInstance */
                    return $shortcutInstance;
                }
            } catch (ReflectionException) {
                throw new ShortcutNotAllowedException(
                    'Shortcut "' . $fqcn . '" was not found or does not implement "' .
                    TcaShortcutInterface::class . '".',
                    1672744035
                );
            }
        }

        throw new MethodNotDefinedException(
            'Method "' . $name . '" is not defined for "' . __CLASS__ . '"',
            1672744033
        );
    }

    protected static function getFQCN($className): string
    {
        return static::getShortcutNamespace() . $className . 'Shortcut';
    }

    protected static function getShortcutNamespace(): string
    {
        return '\\' . __NAMESPACE__ . '\\Shortcut\\';
    }

    public static function translate(string $key): string
    {
        self::$config->injectRuntimeData();
        $identifier = !empty(self::$tmpItemConfig?->identifier ?? '') ? self::$tmpItemConfig->identifier . '.' : '';
        return self::$config->ll() . self::$config->getTablename() . '.' . $identifier . $key;
    }

    public function setDefaultLocalizationFile(string $filename)
    {
        self::getConfig()->setDefaultLocalizationFile($filename);
    }
}
