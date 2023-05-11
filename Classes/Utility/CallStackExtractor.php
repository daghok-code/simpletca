<?php

namespace Febis\SimpleTca\Utility;

use Febis\SimpleTca\Exception\CallstackExtractionException;

class CallStackExtractor
{
    private function __construct()
    {
    }

    /**
     * @throws CallstackExtractionException
     */
    public static function extractFromCallstack(): array
    {
        $stackTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);

        foreach ($stackTrace as $traceItem) {
            preg_match(
                '/[^\/]+\/([^\/]+)\/Configuration\/TCA\/(Overrides\/)?([^\.]+)\.php/',
                $traceItem['file'] ?? '',
                $fileMatch
            );

            if (!empty($fileMatch)) {
                $extkey = $fileMatch[1] ?? null;
                $tablename = $fileMatch[3] ?? null;
                return [$extkey, $tablename];
            }
        }

        throw new CallstackExtractionException();
    }
}
