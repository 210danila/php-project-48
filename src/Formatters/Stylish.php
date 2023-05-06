<?php

namespace Differ\Formatters\Stylish;

const INDENT = '    ';
const VALUE_TYPES = [
    'nested' => 'arrayValue',
    'equal' => 'identialValue',
    'added' => 'addedValue',
    'removed' => 'removedValue'
];

function createOutput(array $diffTree)
{
    $rootChildren = $diffTree['children'];
    $stylishedRootChildren = array_map(
        fn($rootChild) => iteration($rootChild),
        $rootChildren
    );
    return "{\n" . implode("\n", $stylishedRootChildren) . "\n}";
}

function stringifyArray(array $array, int $depth)
{
    $sortedArray = collect($array)->sortKeys()->toArray();

    return collect(array_keys($sortedArray))
        ->map(function ($key) use ($sortedArray, $depth) {
            $value = $sortedArray[$key];
            $prefix = str_repeat(INDENT, $depth);

            if (is_array($value)) {
                $formattedValue = stringifyArray($value, $depth + 1);
                return "{$prefix}{$key}: {\n{$formattedValue}\n{$prefix}}";
            }

            $formattedValue = formatValue($value);
            return "{$prefix}{$key}: {$formattedValue}";
        })
        ->implode("\n");
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

function stringifyDiffNode(string $property, mixed $value, int $depth, string $prefixType)
{
    switch ($prefixType) {
        case 'nested':
            $prefix = str_repeat(INDENT, $depth);
            $formattedValue = collect($value)
                ->map(fn($child) => iteration($child))
                ->implode("\n");
            return "{$prefix}{$property}: {\n{$formattedValue}\n{$prefix}}";

        case 'equal':
            $prefix = str_repeat(INDENT, $depth);
            $formattedValue = formatValue($value);
            return "{$prefix}{$property}: {$formattedValue}";

        case 'removed':
        case 'added':
            $sign = $prefixType === 'added' ? '+' : '-';
            $prefix = str_repeat(INDENT, $depth - 1);
            if (is_array($value)) {
                $formattedValue = stringifyArray($value, $depth + 1);
                return "{$prefix}  {$sign} {$property}: {\n{$formattedValue}\n{$prefix}    }";
            }
            $formattedValue = formatValue($value);
            return "{$prefix}  {$sign} {$property}: {$formattedValue}";

        default:
            throw new \Exception("No such prefix type {$prefixType}.");
    }
}

function iteration(array $diffNode)
{
    $property = $diffNode['property'];
    $depth = $diffNode['depth'];
    $status = $diffNode['status'];

    if ($status === 'updated') {
        return stringifyDiffNode($property, $diffNode['removedValue'], $depth, 'removed') . "\n" .
            stringifyDiffNode($property, $diffNode['addedValue'], $depth, 'added');
    }
    if (!array_key_exists($status, VALUE_TYPES)) {
        throw new \Exception("There is no status with the such name.");
    }
    $valueType = VALUE_TYPES[$status];
    return stringifyDiffNode($property, $diffNode[$valueType], $depth, $status);
}
