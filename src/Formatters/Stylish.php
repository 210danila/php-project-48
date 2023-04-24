<?php

namespace Differ\Formatters\Stylish;

const INDENT = '    ';

function createOutput(array $diffTree)
{
    $rootChildren = $diffTree['children'];
    $stylishedRootChildren = array_map(
        fn($rootChild) => iteration($rootChild),
        $rootChildren
    );
    return "{\n" . implode("\n", $stylishedRootChildren) . "\n}";
}

function stylishArray(array $array, int $depth)
{
    $sortedArray = collect($array)->sortKeys()->toArray();

    $stylishedArray = collect(array_keys($sortedArray))
        ->map(function ($key) use ($sortedArray, $depth) {
            $value = $sortedArray[$key];
            $prefix = str_repeat(INDENT, $depth);

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

function formatValue(mixed $value)
{
    if (is_bool($value)) {
        return $value ? "true" : "false";
    }
    if (is_null($value)) {
        return 'null';
    }
    return $value;
}

function stringifyDiffNode($prefix, $property, $value): string
{
    return "{$prefix}{$property}: {$value}";
}

function iteration(array $diffNode)
{
    $property = $diffNode['property'];
    $depth = $diffNode['depth'];
    $status = $diffNode['status'];
    [$shortPrefix, $fullPrefix] = [str_repeat(INDENT, $depth - 1), str_repeat(INDENT, $depth)];

    $stylishValue = function ($value, $depth, $prefix) {
        if (is_array($value)) {
            $handledValue = stylishArray($value, $depth + 1);
            return "{\n{$handledValue}\n{$prefix}    }";
        }
        return formatValue($value);
    };

    switch ($status) {
        case 'nested':
            $stylishedValue = implode("\n", array_map(
                fn($child) => iteration($child),
                $diffNode['arrayValue']
            ));
            return stringifyDiffNode($fullPrefix, $property, "{\n{$stylishedValue}\n{$fullPrefix}}");

        case 'equal':
            $value = $diffNode['identialValue'];
            return stringifyDiffNode($fullPrefix, $property, formatValue($value));

        case 'updated':
            $stylishedRemovedValue = $stylishValue($diffNode['removedValue'], $depth, $shortPrefix);
            $stylishedAddedValue = $stylishValue($diffNode['addedValue'], $depth, $shortPrefix);
            return stringifyDiffNode("{$shortPrefix}  - ", $property, $stylishedRemovedValue) . "\n" .
                stringifyDiffNode("{$shortPrefix}  + ", $property, $stylishedAddedValue);

        case 'added':
            $stylishedAddedValue = $stylishValue($diffNode['addedValue'], $depth, $shortPrefix);
            return stringifyDiffNode("{$shortPrefix}  + ", $property, $stylishedAddedValue);

        case 'removed':
            $stylishedAddedValue = $stylishValue($diffNode['removedValue'], $depth, $shortPrefix);
            return stringifyDiffNode("{$shortPrefix}  - ", $property, $stylishedAddedValue);

        default:
            throw new \Exception("There is no status with the such name.");
    }
}
