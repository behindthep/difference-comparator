<?php

namespace Diff\Comparator;

use function Functional\sort as fsort;

class Differ
{
    public static function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
    {
        $parsedFile1   = Parsers::parse($pathToFile1);
        $parsedFile2   = Parsers::parse($pathToFile2);
        $difference    = self::makeDiff($parsedFile1, $parsedFile2);
        $formattedDiff = Formatters::makeFormat($difference, $format);
        return $formattedDiff;
    }

    private static function makeDiff(array $parsedFile1, array $parsedFile2): array
    {
        $uniqueKeys = self::getSortedUniqueKeys($parsedFile1, $parsedFile2);
        $difference = array_map(fn($key) => self::checkDifference($key, $parsedFile1, $parsedFile2), $uniqueKeys);
        return $difference;
    }

    private static function checkDifference(mixed $key, array $parsedFile1, array $parsedFile2): array
    {
        $firstValue  = $parsedFile1[$key] ?? null;
        $secondValue = $parsedFile2[$key] ?? null;

        if (is_array($firstValue) && is_array($secondValue)) {
            return [
                'status' => 'nested',
                'key'    => $key,
                'value1' => self::makeDiff($firstValue, $secondValue),
                'value2' => null
            ];
        } elseif (!array_key_exists($key, $parsedFile1)) {
            return [
                'status' => 'added',
                'key'    => $key,
                'value1' => $secondValue,
                'value2' => null
            ];
        } elseif (!array_key_exists($key, $parsedFile2)) {
            return [
                'status' => 'removed',
                'key'    => $key,
                'value1' => $firstValue,
                'value2' => null
            ];
        } elseif ($firstValue === $secondValue) {
            return [
                'status' => 'unchanged',
                'key'    => $key,
                'value1' => $firstValue,
                'value2' => null
            ];
        } else {
            return [
                'status' => 'updated',
                'key'    => $key,
                'value1' => $firstValue,
                'value2' => $secondValue
            ];
        }
    }

    private static function getSortedUniqueKeys(array $associativeArr1, array $associativeArr2): array
    {
        $keys1        = array_keys($associativeArr1);
        $keys2        = array_keys($associativeArr2);
        $combinedKeys = array_merge($keys1, $keys2);
        $uniqueKeys   = array_unique($combinedKeys);
        $sortedKeys   = fsort($uniqueKeys, fn($left, $right) => strcmp($left, $right));
        return $sortedKeys;
    }
}
