<?php

namespace Differ\Formatters\Json;

function createOutput(array $diffTree)
{
    return json_encode(['status' => 'root', 'children' => handleArrayValue($diffTree['children'])]);
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

        case 'nested':
            return ["status" => $status, 'arrayValue' => $values['arrayValue']];

        case 'equal':
            return ["status" => $status, 'identialValue' => $values['identialValue']];

        default:
            throw new \Exception("There is no status with the such name.");
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

    if ($status === "nested") {
        return [
            'status' => 'nested',
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
