<?php

namespace Differ\Stylish;

function makeOutputTree($diffTree)
{
    $rootChildren = $diffTree['children'];
    $stylishedRootChildren = array_map(
        fn($rootChild) => iteration($rootChild),
        $rootChildren
    );
    $stylishedTree = "\n{\n" . implode("\n", $stylishedRootChildren) . "\n}";
    return $stylishedTree;
}

function convertToStrIfBool($value)
{
    if (is_bool($value)) {
        return $value ? "true" : "false";
    } elseif(is_null($value)) {
        return 'null';
    }
    return $value;
}

function stylishArray(array $array, int $depth) {
    ksort($array);

    $stylishedArray = implode("\n", array_map(function ($key) use ($array, $depth) {
        $element = $array[$key];
        $prefix = str_repeat('    ', $depth);

        if (is_array($element)) {
            $stylishedElement = stylishArray($element, $depth + 1);
            return "{$prefix}{$key}: {\n{$stylishedElement}\n{$prefix}}";
        }
        $stylishedElement = convertToStrIfBool($element);
        return "{$prefix}{$key}: {$stylishedElement}";
    }, array_keys($array)));

    return $stylishedArray;
}

function handleElementToRemove(array $element, $key, $depth)
{
    $prefix = str_repeat('    ', $depth - 1);
    $value = $element['value'];

    if ($element['isArray']) {
        $stylishedElement = stylishArray($value, $depth + 1);
        return "{$prefix}  - {$key}: {\n{$stylishedElement}\n{$prefix}    }";
    }
    $stylishedValue = convertToStrIfBool($value);
    return "{$prefix}  - {$key}: {$stylishedValue}";
}

function handleElementToAdd(array $element, $key, $depth)
{
    $prefix = str_repeat('    ', $depth - 1);
    $value = $element['value'];

    if ($element['isArray']) {
        $stylishedElement = stylishArray($value, $depth + 1);
        return "{$prefix}  + {$key}: {\n{$stylishedElement}\n{$prefix}    }";
    }
    $stylishedValue = convertToStrIfBool($value);
    return "{$prefix}  + {$key}: {$stylishedValue}";
}

function handleElementToLeave(array $element, $key, $depth)
{
    $prefix = str_repeat('    ', $depth);
    $value = $element['value'];

    if ($element['isArray']) {
        $stylishedElement = implode("\n", array_map(
            fn($child) => iteration($child),
            $value
        ));
        return "{$prefix}{$key}: {\n{$stylishedElement}\n{$prefix}}";
    }
    $stylishedValue = convertToStrIfBool($value);
    return "{$prefix}{$key}: {$stylishedValue}";
}

function iteration(array $treeElement)
{
    $key = $treeElement['key'];
    $depth = $treeElement['depth'];
    $elementToAdd = $treeElement['addElement'];
    $elementToRemove = $treeElement['removeElement'];
    $elementToLeave = $treeElement['leaveElement'];
    $resultString = '';

    if (!is_null($elementToRemove) && !is_null($elementToAdd)) {
        $resultString .= handleElementToRemove($elementToRemove, $key, $depth) . "\n" .
        handleElementToAdd($elementToAdd, $key, $depth);
    } elseif (!is_null($elementToRemove)) {
        $resultString .= handleElementToRemove($elementToRemove, $key, $depth);
    } elseif (!is_null($elementToAdd)) {
        $resultString .= handleElementToAdd($elementToAdd, $key, $depth);
    }
    if (!is_null($elementToLeave)) {
        $resultString .= handleElementToLeave($elementToLeave, $key, $depth);
    }
    return $resultString;
}