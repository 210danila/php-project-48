<?php

namespace Differ\DiffGenerator;

function generateDiffTree(array $diffData1, array $diffData2)
{
    $diffTree = [
        'property' => '',
        'depth' => 0,
        'children' => iteration($diffData1, $diffData2, 1)
    ];
    return $diffTree;
}

function generateDiffNode(string $property, int $depth, array $values, string $status)
{
    $diffNode = [
        'property' => $property,
        'depth' => $depth,
        'status' => $status,
        'removedValue' => array_key_exists('removeElement', $values) ?
            $values['removeElement'] : null,
        'addedValue' => array_key_exists('addElement', $values) ?
            $values['addElement'] : null,
        'identialValue' => array_key_exists('identialElement', $values) ?
            $values['identialElement'] : null
    ];
    return $diffNode;
}

function handleBothElements(string $property, mixed $beforeValue, mixed $afterValue, int $depth)
{
    [$isBeforeValueArray, $isAfterValueArray] = [is_array($beforeValue), is_array($afterValue)];

    $diffNode = ['property' => $property, 'depth' => $depth];
    if ($isBeforeValueArray && $isAfterValueArray) {
        $diffNode['status'] = 'equal';
        $diffNode['identialValue'] = iteration($beforeValue, $afterValue, $depth + 1);
    } elseif (!$isBeforeValueArray && !$isAfterValueArray && $beforeValue === $afterValue) {
        $diffNode['status'] = 'equal';
        $diffNode['identialValue'] = $beforeValue;
    } else {
        $diffNode['status'] = 'updated';
        $diffNode['removedValue'] = $beforeValue;
        $diffNode['addedValue'] = $afterValue;
    }

    return $diffNode;
}

function handleBeforeElement(string $property, mixed $beforeValue, int $depth)
{
    $diffNode = [
        'property' => $property,
        'depth' => $depth,
        'status' => 'removed',
        'removedValue' => $beforeValue
    ];
    return $diffNode;
}

function handleAfterElement(string $property, mixed $afterValue, int $depth)
{
    $diffNode = [
        'property' => $property,
        'depth' => $depth,
        'status' => 'added',
        'addedValue' => $afterValue
    ];
    return $diffNode;
}

function iteration(array $beforeElement, array $afterElement, int $depth = 1)
{
    $mergedDataKeys = array_keys(array_merge($beforeElement, $afterElement));
    sort($mergedDataKeys);

    $resultElement = array_map(function ($property) use ($beforeElement, $afterElement, $depth) {
        if (array_key_exists($property, $beforeElement) && array_key_exists($property, $afterElement)) {
            $beforeValue = $beforeElement[$property];
            $afterValue = $afterElement[$property];
            return handleBothElements($property, $beforeValue, $afterValue, $depth);
        } elseif (array_key_exists($property, $beforeElement)) {
            $beforeValue = $beforeElement[$property];
            return handleBeforeElement($property, $beforeValue, $depth);
        } else {
            $afterValue = $afterElement[$property];
            return handleAfterElement($property, $afterValue, $depth);
        }
    }, $mergedDataKeys);
    return $resultElement;
}
