<?php

namespace Febis\SimpleTca\Utility;

class TypoScriptHelper
{
    public const TAB_SIZE_TYPOSCRIPT = 2;
    public const TYPOSCRIPT_COUNTING = 10;

    /**
     * Transforms a php object into a readable typoscript notation
     */
    public static function objectToTextualRepresentation(string $key, array $tsObject, int $prevIndent = 0): string
    {
        $currentIndent = $prevIndent + self::TAB_SIZE_TYPOSCRIPT;
        $parts = [];
        $parts[] = self::indent($prevIndent) . $key . ' {';

        foreach ($tsObject as $item) {
            $key = $item[0];
            $value = $item[1];

            if (is_array($value) && count($value) === 2) {
                $parts[] = self::objectToTextualRepresentation($item[0], $item[1], $currentIndent);
            } else {
                $parts[] = self::indent($currentIndent) . $key . ' = ' . $value;
            }
        }

        $parts[] = self::indent($prevIndent) . '}';

        return join("\n", $parts);
    }

    /**
     * Returns space indent
     */
    public static function indent(int $count): string
    {
        return str_repeat(" ", $count);
    }

    /**
     * Transforms snake_format into UpperCamelCase format
     */
    public static function snakeToCamel(string $input): string
    {
        return implode('', array_map('ucfirst', explode('_', $input)));
    }

    public static function transformFromTypedTyposcript(array $input): array
    {
        $output = [];
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                if (isset($value['__type'])) {
                    $output[] = [$key, $value['__type']];
                    unset($value['__type']);
                }

                if (!empty($value)) {
                    $output[] = [$key, self::transformFromTypedTyposcript($value)];
                }
            } else {
                $output[] = [$key, $value];
            }
        }
        return $output;
    }
}
