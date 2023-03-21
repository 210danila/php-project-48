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
    $node = [
        "status" => $status,
    ];

    switch ($status) {
        case 'removed':
            $node['removedValue'] = $values['removed'];
            break;
        case 'added':
            $node['addedValue'] = $values['added'];
            break;
        case 'updated':
            $node['removedValue'] = $values['removed'];
            $node['addedValue'] = $values['added'];
            break;
        case 'equal':
            $node['identialValue'] = $values['idential'];
            break;
        default:
            return "Error: there is no status with the such name.";
    }
    return $node;
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
