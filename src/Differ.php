<?php

namespace Differ\Differ;

use function Differ\Parsers\getParsedData;
use function Differ\DiffGenerator\generateDiffTree;
use function Differ\Formatters\formatDiffTree;

function getFileData(string $filePath)
{
    $content = file($filePath);
    if (is_bool($content)) {
        return 'No such file';
    }
    return file_get_contents($filePath);
}

function genDiff(string $filePath1, string $filePath2, string $formatName = 'stylish')
{
    [$fileData1, $fileData2] = [getFileData($filePath1), getFileData($filePath2)];
    $data1 = getParsedData($fileData1, pathinfo($filePath1, PATHINFO_EXTENSION));
    $data2 = getParsedData($fileData2, pathinfo($filePath2, PATHINFO_EXTENSION));

    $tree = generateDiffTree($data1, $data2);

    $stylishedTree = formatDiffTree($tree, $formatName);
    return $stylishedTree;
}
