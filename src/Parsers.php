<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseData(string $data, string $format)
{
    switch ($format) {
        case 'json':
            return json_decode($data, true);

        case 'yaml':
        case 'yml':
            return Yaml::parse($data);

        default:
            throw new \Exception("There is no such format \"{$format}\".");
    }
}
