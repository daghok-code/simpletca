<?php

namespace Febis\SimpleTca;

use Febis\SimpleTca\Shortcut\AssetShortcut;
use Febis\SimpleTca\Shortcut\ImageShortcut;
use Febis\SimpleTca\Utility\ErrorUtility;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameters)
 */
trait DeprecatedShortcuts
{
    /**
     * @deprecated use TcaGenerator::createFile instead
     */
    public static function createImage(
        $identifier = null,
        $minitems = null,
        $maxitems = null,
        $fieldname = null,
    ): ImageShortcut {
        ErrorUtility::triggerDeprecated('::createImage', TcaGenerator::class . '::createFile');

        /** @phpstan-var ImageShortcut $image */
        $image = static::__callStatic('createImage', func_get_args());
        return $image;
    }

    /**
     * @deprecated use TcaGenerator::createFile instead
     */
    public static function createAsset(
        $identifier = null,
        $minitems = null,
        $maxitems = null,
        $fieldname = null,
    ): AssetShortcut {
        ErrorUtility::triggerDeprecated('::createImage', TcaGenerator::class . '::createFile');

        /** @phpstan-var AssetShortcut $asset */
        $asset = static::__callStatic('createAsset', func_get_args());
        return $asset;
    }
}
