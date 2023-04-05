<?php

namespace Differ\Formatters\Json;

function createOutput(array $diffTree)
{
    $rootChildren = $diffTree['children'];
    $jsonTree = json_encode(handleArrayValue($rootChildren));
    return $jsonTree;
}

function createJsonNode(string $status, array $values)
{
    switch ($status) {
        case 'removed':
            return ["status" => $status, 'removedValue' => $values['removed']];

        case 'added':
            return ["status" => $status, 'addedValue' => $values['added']];

        case 'updated':
            return ["status" => $status, 'removedValue' => $values['removed'], 'addedValue' => $values['added']];

        case 'bothValuesAreArrays':
            return ["status" => $status, 'arrayValue' => $values['arrayValue']];

        case 'identialValues':
            return ["status" => $status, 'identialValue' => $values['identialValue']];

        default:
            return "Error: there is no status with the such name.";
    }
}

function handleArrayValue(array $identialArray)
{
    return array_reduce($identialArray, function ($resultArray, $childNode) {
        $property = $childNode['property'];
        $newResultArray = array_merge([$property => iteration($childNode)], $resultArray);
        return $newResultArray;
    }, []);
}

function iteration(array $diffNode)
{
    $status = $diffNode['status'];

    if ($status === "bothValuesAreArrays") {
        return [
            'status' => 'bothValuesAreArrays',
            'arrayValue' => handleArrayValue($diffNode['arrayValue'])
        ];
    }

    $jsonNode = createJsonNode($status, [
        "added" => $diffNode['addedValue'] ?? null,
        "removed" => $diffNode['removedValue'] ?? null,
        "identialValue" => $diffNode['identialValue'] ?? null
    ]);
    return $jsonNode;
}
