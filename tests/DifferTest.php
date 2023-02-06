<?php

namespace Differ\Tests\DifferTest;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiffWithJson(): void
    {
        $expectedResult01 = file_get_contents('tests/fixtures/result_01_json');
        $actualResult01 = genDiff('tests/fixtures/before_01.json', 'tests/fixtures/after_01.json') . "\n";
        $this->assertEquals($expectedResult01, $actualResult01);

        $expectedResult02 = file_get_contents('tests/fixtures/result_02_json');
        $actualResult02 = genDiff('tests/fixtures/before_02.json', 'tests/fixtures/after_02.json') . "\n";
        $this->assertEquals($expectedResult02, $actualResult02);
    }

    public function testGenDiffWithYaml(): void
    {
        $expectedResult01 = file_get_contents('tests/fixtures/result_01_yaml');
        $actualResult01 = genDiff('tests/fixtures/before_01.yaml', 'tests/fixtures/after_01.yaml') . "\n";
        $this->assertEquals($expectedResult01, $actualResult01);

        $expectedResult02 = file_get_contents('tests/fixtures/result_02_yaml');
        $actualResult02 = genDiff('tests/fixtures/before_02.yml', 'tests/fixtures/after_02.yml') . "\n";
        $this->assertEquals($expectedResult02, $actualResult02);

    }
}
