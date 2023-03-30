<?php

namespace Differ\Formatters\Json;

function createJsonOutput(array $diffTree)
{
    $rootChildren = $diffTree['children'];
    $jsonTree = json_encode(handleIdentialArray($rootChildren));
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
        case 'equal':
            return ["status" => $status, 'identialValue' => $values['idential']];
        default:
            return "Error: there is no status with the such name.";
    }
}

function handleIdentialArray(array $identialArray)
{
    return array_reduce($identialArray, function ($resultArray, $childNode) {
        $property = $childNode['property'];
        $resultArray[$property] = iteration($childNode);
        return $resultArray;
    }, []);
}

function iteration(array $diffNode)
{
    $status = $diffNode['status'];

    if ($status === "equal" && is_array($diffNode['identialValue'])) {
        return [
            'status' => 'equal',
            'identialValue' => handleIdentialArray($diffNode['identialValue'])
        ];
    }

    $jsonNode = createJsonNode($status, [
        "added" => $diffNode['addedValue'] ?? null,
        "removed" => $diffNode['removedValue'] ?? null,
        "idential" => $diffNode['identialValue'] ?? null
    ]);
    return $jsonNode;
}
