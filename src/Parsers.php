<?php

namespace Diff\Comparator;

use Symfony\Component\Yaml\Yaml;

class Parsers
{
    private static function getFileContent(string $pathToFile): string
    {
        $content = is_file($pathToFile)
        ? file_get_contents($pathToFile)
        : exit("File '$pathToFile' not found.\n");

        return $content;
    }

    public static function parse(string $pathToFile): array|string
    {
        $content   = self::getFileContent($pathToFile);
        $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);

        $parsedFile = match ($extension) {
            'json'        => json_decode($content, true),
            'yml', 'yaml' => Yaml::parse($content),
            default       => exit("Unsupported format '$extension' of incoming file.\n"),
        };

        return $parsedFile;
    }
}
