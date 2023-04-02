<?php

namespace Differ\Tests\DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function stylishFmtFilesProvider()
    {
        return [
            [1, 'json'],
            [2, 'yml'],
            [3, 'json'],
            [4, 'yml'],
            [5, 'json'],
            [6, 'yml'],
            [7, 'json'],
            [8, 'yml']
        ];
    }

    public function plainFmtFilesProvider()
    {
        return [
            [5, 'json'],
            [6, 'yml'],
            [7, 'json'],
            [8, 'yml']
        ];
    }

    public function getFixturePath(string $fixtureName)
    {
        return __DIR__ . "/fixtures/{$fixtureName}";
    }

    public function getExpectedFixturePath(string $fixtureName)
    {
        return __DIR__ . "/fixtures/expected/{$fixtureName}";
    }

    /**
     * @dataProvider stylishFmtFilesProvider
     */
    public function testGenDiffWithStylishFormat($fileNumber, $extension): void
    {
        $format = "stylish";
        $expectedFilePath = $this->getExpectedFixturePath("StylishFmt{$fileNumber}");
        $beforeFilePath = $this->getFixturePath("Before{$fileNumber}.{$extension}");
        $afterFilePath = $this->getFixturePath("After{$fileNumber}.{$extension}");

        $actual = genDiff($beforeFilePath, $afterFilePath, $format);
        $this->assertStringEqualsFile($expectedFilePath, $actual);
    }

    /**
     * @dataProvider plainFmtFilesProvider
     */
    public function testGenDiffWithPlainFormat($fileNumber, $extension): void
    {
        $format = 'plain';
        $expectedFilePath = $this->getExpectedFixturePath("PlainFmt{$fileNumber}");
        $beforeFilePath = $this->getFixturePath("Before{$fileNumber}.{$extension}");
        $afterFilePath = $this->getFixturePath("After{$fileNumber}.{$extension}");

        $actual = genDiff($beforeFilePath, $afterFilePath, $format);
        $this->assertStringEqualsFile($expectedFilePath, $actual);
    }

    public function testGenDiffWithJsonFormat(): void
    {
        $format = 'json';
        $expectedFilePath = $this->getExpectedFixturePath("JsonFmt5");
        $beforeFilePath = $this->getFixturePath("Before5.json");
        $afterFilePath = $this->getFixturePath("After5.json");

        $expected = implode(array_map(fn($line) => ltrim($line), file($expectedFilePath)));
        $actual = genDiff($beforeFilePath, $afterFilePath, $format);
        $this->assertEquals(json_decode($expected, true), json_decode($actual, true));
    }
}
