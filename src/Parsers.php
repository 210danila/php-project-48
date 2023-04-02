<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function getParsedData(string $data, string $format)
{
    switch ($format) {
        case 'json':
            return parseJsonString($data);

        case 'yaml':
        case 'yml':
            return parseYamlString($data);

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
