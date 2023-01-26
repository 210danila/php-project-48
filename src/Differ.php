<?php

namespace Differ\Differ;

function genDiff($pathToFile1, $pathToFile2)
{
    $getDataFromFile = function ($pathToFile) {
        return json_decode(implode(
            array_map(fn($line) => ltrim($line), file($pathToFile))
        ), true);
    };

    $formatValueToString = function ($value) {
        if (is_bool($value)) {
            return $value ? "true" : "false";
        }
        return (string) $value;
    };

    $fileData1 = $getDataFromFile($pathToFile1);
    $fileData2 = $getDataFromFile($pathToFile2);
    $mergedData = array_merge($fileData1, $fileData2);
    $mergedDataKeys = array_keys($mergedData);
    sort($mergedDataKeys);

    return array_reduce(
        $mergedDataKeys,
        function ($diffAcc, $key) use ($fileData1, $fileData2, $mergedData, $formatValueToString) {
            if (array_key_exists($key, $fileData1) && array_key_exists($key, $fileData2)) {
                if ($fileData1[$key] === $fileData2[$key]) {
                    return $diffAcc . "    " . (string) $key . ": " . $formatValueToString($mergedData[$key]) . "\n";
                } else {
                    return $diffAcc . "  - " . (string) $key . ": " . $formatValueToString($fileData1[$key]) . "\n" .
                        "  + " . (string) $key . ": " . $formatValueToString($fileData2[$key]) . "\n";
                }
            } elseif (array_key_exists($key, $fileData1)) {
                return $diffAcc . "  - " . (string) $key . ": " . $formatValueToString($fileData1[$key]) . "\n";
            } else {
                return $diffAcc . "  + " . (string) $key . ": " . $formatValueToString($fileData2[$key]) . "\n";
            }
        },
        "\n{\n"
    ) . "}";
}
