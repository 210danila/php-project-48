<?php

namespace Differ\Formatters\Plain;

function createOutput(array $diffTree)
{
    $rootChildren = $diffTree['children'];
    $plainTree = collect($rootChildren)
        ->map(fn($diffNode) => iteration($diffNode, ''))
        ->flatten()
        ->filter()
        ->implode("\n");
    return $plainTree;
}

function formatValue(mixed $value)
{
    if (is_bool($value)) {
        return $value ? "true" : "false";
    } elseif (is_null($value)) {
        return "null";
    } elseif (is_array($value)) {
        return '[complex value]';
    } elseif (is_int($value)) {
        return "{$value}";
    }
    return "'{$value}'";
}

function iteration(array $diffNode, string $actualPath)
{
    $propertyPath = $actualPath === '' ? $diffNode['property'] :
        $actualPath . '.' . $diffNode['property'];
    $status = $diffNode['status'];

    switch ($status) {
        case 'nested':
            $arrayValue = $diffNode['arrayValue'];
            return array_map(fn($childNode) => iteration($childNode, $propertyPath), $arrayValue);

        case 'equal':
            return '';

        case 'updated':
            $removedValue = formatValue($diffNode['removedValue']);
            $addedValue = formatValue($diffNode['addedValue']);
            return "Property '{$propertyPath}' was updated. From {$removedValue} to {$addedValue}";

        case 'added':
            $addedValue = formatValue($diffNode['addedValue']);
            return "Property '{$propertyPath}' was added with value: {$addedValue}";

        case 'removed':
            $removedValue = formatValue($diffNode['removedValue']);
            return "Property '{$propertyPath}' was removed";

        default:
            throw new \Exception("There is no status with the such name.");
    }
}
