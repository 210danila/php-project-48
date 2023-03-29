<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function getParsedData(string $filePath)
{
    $format = pathinfo($filePath, PATHINFO_EXTENSION);

    switch ($format) {
        case 'json':
            $jsonData = implode(array_map(fn($line) => ltrim($line), file($filePath)));
            return parseJsonString($jsonData);

        case 'yaml' || 'yml':
            $yamlData = file_get_contents($filePath);
            return parseYamlString($yamlData);

        default:
            return "No such format \"{$format}\".";
    }
}

function parseJsonString(string $data)
{
    return json_decode($data, true);
}

function parseYamlString(string $data)
{
    return Yaml::parse($data);
}
