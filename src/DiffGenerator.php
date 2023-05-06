<?php

namespace Differ\DiffGenerator;

function generateDiffTree(array $diffData1, array $diffData2)
{
    $diffTree = [
        'status' => 'root',
        'children' => iteration($diffData1, $diffData2, 1)
    ];
    return $diffTree;
}

function generateDiffNode(string $status, string $property, int $depth, array $values)
{
    [$beforeValue, $afterValue] = [$values['beforeValue'], $values['afterValue']];

    switch ($status) {
        case 'nested':
            return [
                'property' => $property,
                'depth' => $depth,
                'status' => 'nested',
                'arrayValue' => iteration($beforeValue, $afterValue, $depth + 1)
            ];
        case 'equal':
            return [
                'property' => $property,
                'depth' => $depth,
                'status' => 'equal',
                'identialValue' => $beforeValue
            ];
        case 'updated':
            return [
                'property' => $property,
                'depth' => $depth,
                'status' => 'updated',
                'removedValue' => $beforeValue,
                'addedValue' => $afterValue
            ];
        case 'removed':
            return [
                'property' => $property,
                'depth' => $depth,
                'status' => 'removed',
                'removedValue' => $beforeValue
            ];
        case 'added':
            return  [
                'property' => $property,
                'depth' => $depth,
                'status' => 'added',
                'addedValue' => $afterValue
            ];
        default:
            throw new \Exception('No such status.');
    }
}

function handleElement(array $elements, int $depth)
{
    [$beforeElement, $afterElement, $mergedKeys] = $elements;
    return array_map(function ($property) use ($beforeElement, $afterElement, $depth) {
        $beforeValue = $beforeElement[$property] ?? null;
        $afterValue = $afterElement[$property] ?? null;
        $values = ['beforeValue' => $beforeValue, 'afterValue' => $afterValue];

        if (!array_key_exists($property, $beforeElement)) {
            return generateDiffNode('added', $property, $depth, $values);
        } elseif (!array_key_exists($property, $afterElement)) {
            return generateDiffNode('removed', $property, $depth, $values);
        } elseif (is_array($beforeValue) && is_array($afterValue)) {
            return generateDiffNode('nested', $property, $depth, $values);
        } elseif ($beforeValue === $afterValue) {
            return generateDiffNode('equal', $property, $depth, $values);
        } elseif ($beforeValue !== $afterValue) {
            return generateDiffNode('updated', $property, $depth, $values);
        } else {
            throw new \Exception('No status found for this values.');
        }
    }, $mergedKeys);
}

function iteration(array $beforeElement, array $afterElement, int $depth = 1)
{
    $mergedDataKeys = array_keys(array_merge($beforeElement, $afterElement));
    $sortedMergedDataKeys = collect($mergedDataKeys)->sort()->toArray();
    $elements = [
        $beforeElement,
        $afterElement,
        $sortedMergedDataKeys
    ];
    return handleElement($elements, $depth);
}
