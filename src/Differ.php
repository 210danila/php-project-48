<?php

namespace Differ\Differ;

use function Differ\Parsers\parseJsonFile;
use function Differ\Parsers\parseYamlFile;

function getParsedData($pathToFile)
{
    $format = pathinfo($pathToFile, PATHINFO_EXTENSION);
    switch ($format) {
        case 'json':
            return parseJsonFile($pathToFile);
        case 'yaml':
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
    $mergedDataKeys = array_keys(array_merge($data1, $data2));
    sort($mergedDataKeys);

    return array_reduce(
        $mergedDataKeys,
        function ($diffAccum, $key) use ($data1, $data2) {
            $element1 = convertToStrIfBool($data1[$key] ?? '');
            $element2 = convertToStrIfBool($data2[$key] ?? '');

            if (array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
                if ($data1[$key] === $data2[$key]) {
                    return "{$diffAccum}    {$key}: {$element1}\n";
                } else {
                    return "{$diffAccum}  - {$key}: {$element1}\n  + {$key}: {$element2}\n";
                }
            } elseif (array_key_exists($key, $data1)) {
                return "{$diffAccum}  - {$key}: {$element1}\n";
            } else {
                return "{$diffAccum}  + {$key}: {$element2}\n";
            }
        },
        "\n{\n"
    ) . "}";
}
