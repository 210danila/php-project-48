<?php

namespace Differ\Differ;

use function Differ\Parsers\getParsedData;
use function Differ\DiffGenerator\generateDiffTree;
use function Differ\Formatters\formatDiffTree;

function genDiff(string $filePath1, string $filePath2, string $formatName = 'stylish')
{
    $data1 = getParsedData($filePath1);
    $data2 = getParsedData($filePath2);

    $tree = generateDiffTree($data1, $data2);

    $stylishedTree = formatDiffTree($tree, $formatName);
    return $stylishedTree;
}
