<?php

namespace Differ\Formatters\Stylish;

function createOutput(array $diffTree)
{
    $rootChildren = $diffTree['children'];
    $stylishedRootChildren = array_map(
        fn($rootChild) => stringifyDiffNode($rootChild),
        $rootChildren
    );
    return "{\n" . implode("\n", $stylishedRootChildren) . "\n}";
}

function createPrefix($depth)
{
    return str_repeat('    ', $depth);
}

function stringifyArray(array $array, int $depth)
{
    $sortedArray = collect($array)->sortKeys()->toArray();

    return collect(array_keys($sortedArray))
        ->map(function ($key) use ($sortedArray, $depth) {
            $value = $sortedArray[$key];
            $prefix = createPrefix($depth);

            if (is_array($value)) {
                $formattedValue = stringifyArray($value, $depth + 1);
                return "{$prefix}{$key}: {\n{$formattedValue}\n{$prefix}}";
            }

            $formattedValue = formatValue($value);
            return "{$prefix}{$key}: {$formattedValue}";
        })
        ->implode("\n");
}

function formatValue(mixed $value, int $depth = null)
{
    if (is_bool($value)) {
        return $value ? "true" : "false";
    }
    if (is_null($value)) {
        return 'null';
    }
    if (is_array($value)) {
        if (is_null($depth)) {
            throw new \Exception('$depth parameter is required.');
        }
        $stringifiedArray = stringifyArray($value, $depth + 1);
        $prefix = createPrefix($depth);
        return "{\n{$stringifiedArray}\n{$prefix}}";
    }
    return $value;
}

function stringifyDiffNode(array $diffNode)
{
    ['property' => $property, 'depth' => $depth, 'status' => $status] = $diffNode;
    $prefix = in_array($status, ['nested', 'equal']) ?
        createPrefix($depth) : createPrefix($depth - 1);

    switch ($status) {
        case 'nested':
            $formattedValue = collect($diffNode['arrayValue'])
                ->map(fn($child) => stringifyDiffNode($child))
                ->implode("\n");
            return "{$prefix}{$property}: {\n{$formattedValue}\n{$prefix}}";

        case 'equal':
            $formattedValue = formatValue($diffNode['identialValue']);
            return "{$prefix}{$property}: {$formattedValue}";

        case 'updated':
            $formattedRemovedValue = formatValue($diffNode['removedValue'], $depth);
            $formattedAddedValue = formatValue($diffNode['addedValue'], $depth);
            return "{$prefix}  - {$property}: {$formattedRemovedValue}" . "\n" .
                "{$prefix}  + {$property}: {$formattedAddedValue}";

        case 'removed':
        case 'added':
            $sign = $status === 'added' ? '+' : '-';
            $value = $diffNode["{$status}Value"];
            $formattedValue = formatValue($value, $depth);
            return "{$prefix}  {$sign} {$property}: {$formattedValue}";

        default:
            throw new \Exception("No such status {$status}.");
    }
}
