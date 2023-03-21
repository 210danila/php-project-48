<?php

namespace Differ\Tests\DifferTest;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function extensionProvider()
    {
        return [
            ['json'],
            ['yml']
        ];
    }

    public function getFlatFixturePath(string $fixtureName)
    {
        return "tests/fixtures/flat_structure/{$fixtureName}";
    }

    public function getNestedFixturePath(string $fixtureName)
    {
        return "tests/fixtures/nested_structure/{$fixtureName}";
    }

    public function getNestedExpectedFixturePath(string $fixtureName)
    {
        return "tests/fixtures/nested_structure/expected/{$fixtureName}";
    }

    /**
     * @dataProvider extensionProvider
     */
    public function testGenDiffWithFlatStructure($extension): void
    {
        $flatTestsCount = 10;

        for ($testCounter = 1; $testCounter <= $flatTestsCount; $testCounter++) {
            if (!is_file($this->getFlatFixturePath("Expected{$testCounter}_{$extension}"))) {
                continue;
            }

            $expectedFilePath = $this->getFlatFixturePath("Expected{$testCounter}_{$extension}");
            $beforeFilePath = $this->getFlatFixturePath("Before{$testCounter}.{$extension}");
            $afterFilePath = $this->getFlatFixturePath("After{$testCounter}.{$extension}");

            $expected = file_get_contents($expectedFilePath);
            $actual = genDiff($beforeFilePath, $afterFilePath);
            $this->assertEquals($expected, $actual);
        }
    }

    /**
     * @dataProvider extensionProvider
     */
    public function testGenDiffWithStylishFormat($extension): void
    {
        $nestedTestsCount = 10;

        for ($testCounter = 1; $testCounter <= $nestedTestsCount; $testCounter++) {
            if (!is_file($this->getNestedExpectedFixturePath("StylishFmt{$testCounter}_{$extension}"))) {
                continue;
            }

            $expectedFilePath = $this->getNestedExpectedFixturePath("StylishFmt{$testCounter}_{$extension}");
            $beforeFilePath = $this->getNestedFixturePath("Before{$testCounter}.{$extension}");
            $afterFilePath = $this->getNestedFixturePath("After{$testCounter}.{$extension}");

            $expected = file_get_contents($expectedFilePath);
            $actual= genDiff($beforeFilePath, $afterFilePath);
            $this->assertEquals($expected, $actual);
        }
    }

    /**
     * @dataProvider extensionProvider
     */
    public function testGenDiffWithPlainFormat($extension): void
    {
        $format = 'plain';
        $nestedTestsCount = 10;

        for ($testCounter = 1; $testCounter <= $nestedTestsCount; $testCounter++) {
            if (!is_file($this->getNestedExpectedFixturePath("PlainFmt{$testCounter}_{$extension}"))) {
                continue;
            }

            $expectedFilePath = $this->getNestedExpectedFixturePath("PlainFmt{$testCounter}_{$extension}");
            $beforeFilePath = $this->getNestedFixturePath("Before{$testCounter}.{$extension}");
            $afterFilePath = $this->getNestedFixturePath("After{$testCounter}.{$extension}");

            $expected= file_get_contents($expectedFilePath);
            $actual= genDiff($beforeFilePath, $afterFilePath, $format);
            $this->assertEquals($expected, $actual);
        }
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
