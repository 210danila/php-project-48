<?php

namespace Differ\Formatters;

use function Differ\Formatters\Json\createJsonOutput;
use function Differ\Formatters\Plain\createPlainOutput;
use function Differ\Formatters\Stylish\createStylishedOutput;

function formatDiffTree(array $diffTree, string $formatName)
{
    switch ($formatName) {
        case 'stylish':
            return createStylishedOutput($diffTree);
        case 'plain':
            return createPlainOutput($diffTree);
        case 'json':
            return createJsonOutput($diffTree);
        default:
            return "Error: There is no format called {$formatName}.";
    }
}
