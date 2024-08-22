<?php

namespace Febis\SimpleTca;

use Febis\SimpleTca\Exception\CallstackExtractionException;
use Febis\SimpleTca\Exception\MethodNotDefinedException;
use Febis\SimpleTca\Exception\ShortcutNotAllowedException;
use Febis\SimpleTca\Shortcut\AbstractShortcut;
use Febis\SimpleTca\Shortcut\AssetShortcut;
use Febis\SimpleTca\Shortcut\CheckboxShortcut;
use Febis\SimpleTca\Shortcut\FileShortcut;
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
use ReflectionClass;
use ReflectionException;

/**
 * @codingStandardsIgnoreStart
 * @method static FileShortcut createFile($identifier = null, $minitems = null, $maxitems = null, $allowed = null)
 * @method static CheckboxShortcut createCheckbox($identifier = null, $renderType = null)
 * @method static InputShortcut createInput($identifier = null, $eval = null, $renderType = null)
 * @method static IRREShortcut createIRRE($identifier = null, $foreignTable = null, $minitems = null, $maxitems = null)
 * @method static LinkShortcut createLink($identifier = null)
 * @method static PassthroughShortcut createPassthrough($identifier = null)
 * @method static RelationShortcut createRelation($identifier = null, $allowed = null, $size = null, $minitems = null, $maxitems = null)
 * @method static RelationMMShortcut createRelationMM($identifier = null, $allowed = null, $mM = null, $mMOppositeField = null, $size = null, $minitems = null, $maxitems = null)
 * @method static RteShortcut createRte($identifier = null)
 * @method static SelectSingleShortcut createSelectSingle($identifier = null, $items = null, $renderType = null)
 * @method static SlugShortcut createSlug($identifier = null, $size = null, $eval = null)
 * @codingStandardsIgnoreEnd
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShortcutImplementation
{
    use ConfigTrait;
    use DeprecatedShortcuts;

    /**
     * Calls Shortcut Methods
     * @throws MethodNotDefinedException
     * @throws ShortcutNotAllowedException|CallstackExtractionException
     */
    public static function __callStatic(string $name, array $arguments): TcaShortcutInterface
    {
        preg_match('/\Acreate([A-Z][A-z0-9]+)\z/', $name, $match);

        if (isset($match[0], $match[1])) {
            $fqcn = self::getFQCN($match[1]);
            try {
                $reflectionClass = new ReflectionClass($fqcn);
                $shortcutInstance = $reflectionClass->newInstance(...$arguments);
                if ($reflectionClass->implementsInterface(TcaShortcutInterface::class)) {
                    if ($shortcutInstance instanceof AbstractShortcut) {
                        $shortcutInstance->withTablename(static::getConfig()->getTablename());
                    }

                    /** @var TcaShortcutInterface $shortcutInstance */
                    return $shortcutInstance;
                }
            } catch (ReflectionException) {
                throw new ShortcutNotAllowedException(
                    'Shortcut "' . $fqcn . '" was not found or does not implement "' .
                    TcaShortcutInterface::class . '".',
                    1672744035,
                );
            }
        }

        throw new MethodNotDefinedException(
            'Method "' . $name . '" is not defined for "' . self::class . '"',
            1672744033,
        );
    }

    protected static function getFQCN($className): string
    {
        return self::getShortcutNamespace() . $className . 'Shortcut';
    }

    protected static function getShortcutNamespace(): string
    {
        return '\\' . __NAMESPACE__ . '\\Shortcut\\';
    }
}
