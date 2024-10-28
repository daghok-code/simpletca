<?php

namespace Febis\SimpleTca;

use Febis\SimpleTca\Utility\ErrorUtility;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameters)
 */
trait DeprecatedShortcuts
{
    ///**
    // * @deprecated use TcaGenerator::createXY instead
    // */
    //public static function createYZ(
    //    $identifier = null,
    //    $minitems = null,
    //    $maxitems = null,
    //    $fieldname = null,
    //): XYShortcut {
    //    ErrorUtility::triggerDeprecated('::createYZ', TcaGenerator::class . '::createXY');
    //
    //    /** @phpstan-var XYShortcut $image */
    //    $xy = static::__callStatic('createXY', func_get_args());
    //    return $xy;
    //}
}
