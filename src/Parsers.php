<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseJsonFile($pathToFile)
{
    return json_decode(implode(
        array_map(fn($line) => ltrim($line), file($pathToFile))
    ), true);
}

function parseYamlFile($pathToFile)
{
    return Yaml::parseFile($pathToFile);
}
