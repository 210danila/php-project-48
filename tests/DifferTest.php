<?php

namespace Differ\Tests\DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function stylishFmtFilesProvider()
    {
        return [
            ['After1.json', 'Before1.json', 'expected/StylishFmt1'],
            ['After2.yml', 'Before2.yml', 'expected/StylishFmt2'],
            ['After3.json', 'Before3.json', 'expected/StylishFmt3'],
            ['After4.yml', 'Before4.yml', 'expected/StylishFmt4'],
            ['After5.json', 'Before5.json', 'expected/StylishFmt5'],
            ['After6.yml', 'Before6.yml', 'expected/StylishFmt6'],
            ['After7.json', 'Before7.json', 'expected/StylishFmt7'],
            ['After8.yml', 'Before8.yml', 'expected/StylishFmt8']
        ];
    }

    public function plainFmtFilesProvider()
    {
        return [
            ['After5.json', 'Before5.json', 'expected/PlainFmt5'],
            ['After6.yml', 'Before6.yml', 'expected/PlainFmt6'],
            ['After7.json', 'Before7.json', 'expected/PlainFmt7'],
            ['After8.yml', 'Before8.yml', 'expected/PlainFmt8']
        ];
    }

    public function jsonFmtFilesProvider()
    {
        return[
            ['After5.json', 'Before5.json', 'expected/JsonFmt5']
        ];
    }

    public function getFixturePath(string $fixtureName)
    {
        return __DIR__ . "/fixtures/{$fixtureName}";
    }

    /**
     * @dataProvider stylishFmtFilesProvider
     */
    public function testGenDiffWithStylishFormat($afterFilePath, $beforeFilePath, $expectedFilePath): void
    {
        $format = "stylish";
        $expectedFilePath = $this->getFixturePath($expectedFilePath);
        $beforeFilePath = $this->getFixturePath($beforeFilePath);
        $afterFilePath = $this->getFixturePath($afterFilePath);

        $actual = genDiff($beforeFilePath, $afterFilePath, $format);
        $this->assertStringEqualsFile($expectedFilePath, $actual);
    }

    /**
     * @dataProvider plainFmtFilesProvider
     */
    public function testGenDiffWithPlainFormat($afterFilePath, $beforeFilePath, $expectedFilePath): void
    {
        $format = 'plain';
        $expectedFilePath = $this->getFixturePath($expectedFilePath);
        $beforeFilePath = $this->getFixturePath($beforeFilePath);
        $afterFilePath = $this->getFixturePath($afterFilePath);

        $actual = genDiff($beforeFilePath, $afterFilePath, $format);
        $this->assertStringEqualsFile($expectedFilePath, $actual);
    }

    /**
     * @dataProvider jsonFmtFilesProvider
     */
    public function testGenDiffWithJsonFormat($afterFilePath, $beforeFilePath, $expectedFilePath): void
    {
        $format = 'json';
        $expectedFilePath = $this->getFixturePath($expectedFilePath);
        $beforeFilePath = $this->getFixturePath($beforeFilePath);
        $afterFilePath = $this->getFixturePath($afterFilePath);

        $expected = implode(array_map(fn($line) => ltrim($line), file($expectedFilePath)));
        $expectedFileLines = file($expectedFilePath);
        $expected = array_reduce($expectedFileLines, function ($resultString, $line) {
            return $resultString . $line;
        }, '');
        $actual = genDiff($beforeFilePath, $afterFilePath, $format);
        $this->assertEquals(json_decode($expected, true), json_decode($actual, true));
    }
}
