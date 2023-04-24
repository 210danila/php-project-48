<?php

namespace Differ\Formatters;

function formatDiffTree(array $diffTree, string $formatName)
{
    switch ($formatName) {
        case 'stylish':
            return \Differ\Formatters\Stylish\createOutput($diffTree);

        case 'plain':
            return \Differ\Formatters\Plain\createOutput($diffTree);

        case 'json':
            return \Differ\Formatters\Json\createOutput($diffTree);

        default:
            throw new \Exception("There is no format called {$formatName}.");
    }
}
