<?php

namespace Febis\SimpleTca\Utility;

final readonly class ErrorUtility
{
    public static function triggerDeprecated($deprecated, $alternative): void
    {
        trigger_error(
            $deprecated . ' is deprecated and will be removed in upcoming versions. Use instead ' . $alternative,
            E_USER_DEPRECATED,
        );
    }
}
