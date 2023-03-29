<?php

namespace Differ\Tests\DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function nestedFilesProvider()
    {
        return [
            [1, 'json'],
            [1, 'yml'],
            [2, 'json'],
            [2, 'yml']
        ];
    }
    public function flatFilesProvider()
    {
        return [
            ['Before1.json', 'After1.json', 'Expected1_json'],
            ['Before1.yml', 'After1.yml', 'Expected1_yml'],
            ['Before2.json', 'After2.json', 'Expected2_json'],
            ['Before2.yml', 'After2.yml', 'Expected2_yml']
        ];
    }

    public function getFlatFixturePath(string $fixtureName)
    {
        return __DIR__ . "/fixtures/flat_structure/{$fixtureName}";
    }

    public function getNestedFixturePath(string $fixtureName)
    {
        return __DIR__ . "/fixtures/nested_structure/{$fixtureName}";
    }

    public function getNestedExpectedFixturePath(string $fixtureName)
    {
        return __DIR__ . "/fixtures/nested_structure/expected/{$fixtureName}";
    }

    /**
     * @dataProvider flatFilesProvider
     */
    public function testGenDiffWithFlatStructure($beforeFileName, $afterFileName, $expectedFileName): void
    {
        $beforeFilePath = $this->getFlatFixturePath($beforeFileName);
        $afterFilePath = $this->getFlatFixturePath($afterFileName);
        $expectedFilePath = $this->getFlatFixturePath($expectedFileName);

        $expected = file_get_contents($expectedFilePath);
        $actual = genDiff($beforeFilePath, $afterFilePath);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider nestedFilesProvider
     */
    public function testGenDiffWithStylishFormat($fileNumber, $extension): void
    {
        $format = "stylish";
        $expectedFilePath = $this->getNestedExpectedFixturePath("StylishFmt{$fileNumber}_{$extension}");
        $beforeFilePath = $this->getNestedFixturePath("Before{$fileNumber}.{$extension}");
        $afterFilePath = $this->getNestedFixturePath("After{$fileNumber}.{$extension}");

        $actual = genDiff($beforeFilePath, $afterFilePath, $format);
        $this->assertStringEqualsFile($expectedFilePath, $actual);
    }

    /**
     * @dataProvider nestedFilesProvider
     */
    public function testGenDiffWithPlainFormat($fileNumber, $extension): void
    {
        $format = 'plain';
        $expectedFilePath = $this->getNestedExpectedFixturePath("PlainFmt{$fileNumber}_{$extension}");
        $beforeFilePath = $this->getNestedFixturePath("Before{$fileNumber}.{$extension}");
        $afterFilePath = $this->getNestedFixturePath("After{$fileNumber}.{$extension}");

        $actual = genDiff($beforeFilePath, $afterFilePath, $format);
        $this->assertStringEqualsFile($expectedFilePath, $actual);
    }

    public function testGenDiffWithJsonFormat(): void
    {
        $format = 'json';
        $expectedFilePath = $this->getNestedExpectedFixturePath("JsonFmt1.json");
        $beforeFilePath = $this->getNestedFixturePath("Before1.json");
        $afterFilePath = $this->getNestedFixturePath("After1.json");

        $expected = implode(array_map(fn($line) => ltrim($line), file($expectedFilePath)));
        $actual = genDiff($beforeFilePath, $afterFilePath, $format);
        $this->assertEquals(json_decode($expected, true), json_decode($actual, true));
    }
}
