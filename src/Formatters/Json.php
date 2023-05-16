<?php

namespace Differ\Formatters\Json;

function createOutput(array $diffTree)
{
    return json_encode($diffTree);
}
