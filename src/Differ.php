<?php

namespace Differ\Differ;

use function Differ\Parsers\parseData;
use function Differ\DiffGenerator\generateDiffTree;
use function Differ\Formatters\formatDiffTree;

function getFileData(string $filePath)
{
    $content = file_get_contents($filePath);
    if (is_bool($content)) {
        throw new \Exception("No such file \"{$filePath}\".");
    }
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    return [$content, $extension];
}

function genDiff(string $filePath1, string $filePath2, string $formatName = 'stylish')
{
    [$fileData1, $fileData2] = [
        getFileData($filePath1),
        getFileData($filePath2)
    ];
    [$data1, $data2] = [
        parseData(...$fileData1),
        parseData(...$fileData2)
    ];

    $tree = generateDiffTree($data1, $data2);
    return formatDiffTree($tree, $formatName);
}
