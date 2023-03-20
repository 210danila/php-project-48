<?php

namespace Differ\Differ;

use function Differ\Parsers\parseJsonFile;
use function Differ\Parsers\parseYamlFile;
use function Differ\DiffGenerator\generateDiffTree;
use function Differ\Formatters\formatDiffTree;

function getParsedData(string $filePath)
{
    $format = pathinfo($filePath, PATHINFO_EXTENSION);
    switch ($format) {
        case 'json':
            return parseJsonFile($filePath);
        case 'yaml' || 'yml':
            return parseYamlFile($filePath);
        default:
            return false;
    }
}

function genDiff(string $filePath1, string $filePath2, string $formatName = 'stylish')
{
    $data1 = getParsedData($filePath1);
    $data2 = getParsedData($filePath2);

    $tree = generateDiffTree($data1, $data2);

    $stylishedTree = formatDiffTree($tree, $formatName);
    return $stylishedTree;
}
