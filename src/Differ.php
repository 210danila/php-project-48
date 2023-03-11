<?php

namespace Differ\Differ;

use function Differ\Parsers\parseJsonFile;
use function Differ\Parsers\parseYamlFile;
use function Differ\DiffFinder\createDiffTree;
use function Differ\Stylish\makeOutputTree;

function getParsedData($pathToFile)
{
    $format = pathinfo($pathToFile, PATHINFO_EXTENSION);
    switch ($format) {
        case 'json':
            return parseJsonFile($pathToFile);
        case 'yaml' || 'yml':
            return parseYamlFile($pathToFile);
        default:
            return false;
    }
}

function convertToStrIfBool($value)
{
    if (is_bool($value)) {
        return $value ? "true" : "false";
    }
    return $value;
}

function genDiff($pathToFile1, $pathToFile2)
{
    $data1 = getParsedData($pathToFile1);
    $data2 = getParsedData($pathToFile2);

    $tree = createDiffTree($data1, $data2);
    $stylisedTree = makeOutputTree($tree);
    return $stylisedTree;
}
