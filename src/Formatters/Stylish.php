<?php

namespace Differ\Formatters\Stylish;

const INDENT = '    ';

function createStylishedOutput(array $diffTree)
{
    $rootChildren = $diffTree['children'];
    $stylishedRootChildren = array_map(
        fn($rootChild) => iteration($rootChild),
        $rootChildren
    );
    $stylishedTree = "{\n" . implode("\n", $stylishedRootChildren) . "\n}";
    return $stylishedTree;
}

function formatValue(mixed $value)
{
    if (is_bool($value)) {
        return $value ? "true" : "false";
    } elseif (is_null($value)) {
        return 'null';
    }
    return $value;
}

function createPrefix(int $depth)
{
    return str_repeat(INDENT, $depth);
}

function stylishArray(array $array, int $depth)
{
    $sortedArray = collect($array)->sortKeys()->toArray();

    $stylishedArray = collect(array_keys($sortedArray))
        ->map(function ($key) use ($sortedArray, $depth) {
            $value = $sortedArray[$key];
            $prefix = createPrefix($depth);

            if (is_array($value)) {
                $stylishedValue = stylishArray($value, $depth + 1);
                return "{$prefix}{$key}: {\n{$stylishedValue}\n{$prefix}}";
            }

            $stylishedValue = formatValue($value);
            return "{$prefix}{$key}: {$stylishedValue}";
        })
        ->implode("\n");
    return $stylishedArray;
}

function handleRemovedOrAddedValue(mixed $value, string $property, int $depth, string $sign)
{
    $prefix = createPrefix($depth - 1);

    if (is_array($value)) {
        $stylishedValue = stylishArray($value, $depth + 1);
        return "{$prefix}  {$sign} {$property}: {\n{$stylishedValue}\n{$prefix}    }";
    }

    $stylishedValue = formatValue($value);
    return "{$prefix}  {$sign} {$property}: {$stylishedValue}";
}

function handleIdentialValue(mixed $value, string $property, int $depth)
{
    $prefix = createPrefix($depth);

    if (is_array($value)) {
        $stylishedValue = implode("\n", array_map(
            fn($child) => iteration($child),
            $value
        ));
        return "{$prefix}{$property}: {\n{$stylishedValue}\n{$prefix}}";
    }

    $stylishedValue = formatValue($value);
    return "{$prefix}{$property}: {$stylishedValue}";
}

function iteration(array $diffNode)
{
    $property = $diffNode['property'];
    $depth = $diffNode['depth'];
    $status = $diffNode['status'];

    switch ($status) {
        case 'equal':
            return handleIdentialValue($diffNode['identialValue'], $property, $depth);

        case 'updated':
            return handleRemovedOrAddedValue($diffNode['removedValue'], $property, $depth, '-') . "\n" .
                handleRemovedOrAddedValue($diffNode['addedValue'], $property, $depth, '+');

        case 'added':
            return handleRemovedOrAddedValue($diffNode['addedValue'], $property, $depth, '+');

        case 'removed':
            return handleRemovedOrAddedValue($diffNode['removedValue'], $property, $depth, '-');

        default:
            return "Error: there is no status with the such name.";
    }
}
