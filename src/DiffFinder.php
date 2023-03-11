<?php

namespace Differ\DiffFinder;

function createDiffTree($diffData1, $diffData2) 
{
    $diffTree = [
        'key' => '',
        'depth' => 0,
        'children' => iteration($diffData1, $diffData2, 1)
    ];
    return $diffTree;
}

function handleBothElements($key, $beforeValue, $afterValue, $depth)
{
    [$isBeforeValueArray, $isAfterValueArray] = [is_array($beforeValue), is_array($afterValue)];

    if ($isBeforeValueArray && $isAfterValueArray) {
        return [
            'key' => $key,
            'depth' => $depth,
            'removeElement' => null,
            'addElement' => null,
            'leaveElement' => [
                'isArray' => true,
                'value' => iteration($beforeValue, $afterValue, $depth + 1)
            ]
        ];
    } elseif (!$isBeforeValueArray && !$isAfterValueArray && $beforeValue === $afterValue) {
        return [
            'key' => $key,
            'depth' => $depth,
            'removeElement' => null,
            'addElement' => null,
            'leaveElement' => ['isArray' => false, 'value' => $beforeValue]
        ];
    } else {
        return [
            'key' => $key,
            'depth' => $depth,
            'removeElement' => ['isArray' => $isBeforeValueArray, 'value' => $beforeValue],
            'addElement' => ['isArray' => $isAfterValueArray, 'value' => $afterValue],
            'leaveElement' => null
        ];
    }
}

function handleBeforeElement($key, $beforeValue, $depth)
{
    $isBeforeValueArray = is_array($beforeValue);
    return [
        'key' => $key,
        'depth' => $depth,
        'removeElement' => ['isArray' => $isBeforeValueArray ,'value' => $beforeValue],
        'addElement' => null,
        'leaveElement' => null
    ];
}

function handleAfterElement($key, $afterValue, $depth)
{
    $isAfterValueArray = is_array($afterValue);
    return [
        'key' => $key,
        'depth' => $depth,
        'removeElement' => null,
        'addElement' => ['isArray' => $isAfterValueArray ,'value' => $afterValue],
        'leaveElement' => null
    ];
}

function iteration(array $beforeElement, array $afterElement, int $depth = 1)
{
    $mergedDataKeys = array_keys(array_merge($beforeElement, $afterElement));
    sort($mergedDataKeys);

    $resultElement = array_map(function ($key) use ($beforeElement, $afterElement, $depth) {
        if (array_key_exists($key, $beforeElement) && array_key_exists($key, $afterElement)) {
            $beforeValue = $beforeElement[$key];
            $afterValue = $afterElement[$key];
            return handleBothElements($key, $beforeValue, $afterValue, $depth);
        } elseif (array_key_exists($key, $beforeElement)){
            $beforeValue = $beforeElement[$key];
            return handleBeforeElement($key, $beforeValue, $depth);
        } else {
            $afterValue = $afterElement[$key];
            return handleAfterElement($key, $afterValue, $depth);
        }
    }, $mergedDataKeys);
    return $resultElement;
}
