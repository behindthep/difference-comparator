<?php

namespace Diff\Comparator\Formatters;

class Stylish
{
    public static function stylishFormat(array $difference): string
    {
        $formattedDiff   = self::makeStringsFromDiff($difference);
        $stringifiedDiff = implode("\n", $formattedDiff);

        return "{\n{$stringifiedDiff}\n}\n";
    }

    private static function makeStringsFromDiff(array $difference, int $level = 0): array
    {
        $spaces    = self::getSpaces($level);
        $nextLevel = $level + 1;

        return array_map(function ($node) use ($spaces, $nextLevel) {
            [
                'status' => $status,
                'key'    => $key,
                'value1' => $value1,
                'value2' => $value2
            ] = $node;

            return match ($status) {
                'nested'    => self::formatNested($key, $value1, $spaces, $nextLevel),
                'unchanged' => self::formatUnchanged($key, $value1, $spaces, $nextLevel),
                'added'     => self::formatAdded($key, $value1, $spaces, $nextLevel),
                'removed'   => self::formatRemoved($key, $value1, $spaces, $nextLevel),
                default     => self::formatUpdated($key, $value1, $value2, $spaces, $nextLevel)
            };
        }, $difference);
    }

    private static function formatNested(mixed $key, mixed $value, string $spaces, int $nextLevel): string
    {
        $nested = self::makeStringsFromDiff($value, $nextLevel);
        $stringifiedNest = implode("\n", $nested);
        return "$spaces    $key: {\n{$stringifiedNest}\n{$spaces}    }";
    }

    private static function formatUnchanged(mixed $key, mixed $value, string $spaces, int $nextLevel): string
    {
        $stringifiedValue = self::stringifyValue($value, $nextLevel);
        return "$spaces    $key: $stringifiedValue";
    }

    private static function formatAdded(mixed $key, mixed $value, string $spaces, int $nextLevel): string
    {
        $stringifiedValue = self::stringifyValue($value, $nextLevel);
        return "$spaces  + $key: $stringifiedValue";
    }

    private static function formatRemoved(mixed $key, mixed $value, string $spaces, int $nextLevel): string
    {
        $stringifiedValue = self::stringifyValue($value, $nextLevel);
        return "$spaces  - $key: $stringifiedValue";
    }

    private static function formatUpdated(
        mixed $key,
        mixed $value1,
        mixed $value2,
        string $spaces,
        int $nextLevel
    ): string {
        $stringifiedValue1 = self::stringifyValue($value1, $nextLevel);
        $stringifiedValue2 = self::stringifyValue($value2, $nextLevel);
        return "$spaces  - $key: {$stringifiedValue1}\n{$spaces}  + $key: $stringifiedValue2";
    }

    private static function getSpaces(int $numberOfRepetitions): string
    {
        return str_repeat('    ', $numberOfRepetitions);
    }

    private static function stringifyValue(mixed $value, int $level): mixed
    {
        switch (true) {
            case is_null($value):
                return 'null';
            case is_bool($value):
                return $value ? 'true' : 'false';
            case is_array($value):
                $stringifiedArr = self::convertArrayToString($value, $level);
                $spaces         = self::getSpaces($level);
                return "{{$stringifiedArr}\n{$spaces}}";
            default:
                return "$value";
        }
    }

    private static function convertArrayToString(array $arr, int $level): string
    {
        $nextLevel = $level + 1;

        $formattedArr = array_map(function ($key) use ($arr, $nextLevel) {
            $value  = self::stringifyValue($arr[$key], $nextLevel);
            $spaces = self::getSpaces($nextLevel);
            return "\n{$spaces}{$key}: $value";
        }, array_keys($arr));

        return implode('', $formattedArr);
    }
}
