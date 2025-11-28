<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Utils\DiffMergeStrategies;
use Skpassegna\Json\Enums\DiffMergeStrategy;

class DiffMergeStrategiesTest extends TestCase
{
    public function testMergeRecursive(): void
    {
        $target = ['a' => 1, 'b' => ['c' => 2]];
        $source = ['b' => ['d' => 3], 'e' => 4];

        $result = DiffMergeStrategies::mergeRecursive($target, $source);

        $this->assertEquals(1, $result['a']);
        $this->assertEquals(2, $result['b']['c']);
        $this->assertEquals(3, $result['b']['d']);
        $this->assertEquals(4, $result['e']);
    }

    public function testMergeDeep(): void
    {
        $target = ['a' => [1, 2], 'b' => ['x' => 1]];
        $source = ['a' => [3], 'b' => ['y' => 2]];

        $result = DiffMergeStrategies::mergeDeep($target, $source);

        $this->assertEquals([1, 2, 3], $result['a']);
        $this->assertEquals(['x' => 1, 'y' => 2], $result['b']);
    }

    public function testMergeReplace(): void
    {
        $target = ['a' => 1, 'b' => 2];
        $source = ['c' => 3];

        $result = DiffMergeStrategies::mergeReplace($target, $source);

        $this->assertEquals(['c' => 3], $result);
    }

    public function testMergeShallow(): void
    {
        $target = ['a' => 1, 'b' => ['c' => 2]];
        $source = ['b' => ['d' => 3], 'e' => 4];

        $result = DiffMergeStrategies::mergeShallow($target, $source);

        $this->assertEquals(1, $result['a']);
        $this->assertEquals(['d' => 3], $result['b']);
        $this->assertEquals(4, $result['e']);
    }

    public function testMergeRfc7396(): void
    {
        $target = ['a' => 1, 'b' => 2, 'c' => 3];
        $source = ['b' => null, 'd' => 4];

        $result = DiffMergeStrategies::mergeRfc7396($target, $source);

        $this->assertEquals(1, $result['a']);
        $this->assertArrayNotHasKey('b', $result);
        $this->assertEquals(3, $result['c']);
        $this->assertEquals(4, $result['d']);
    }

    public function testMergeRfc7396NestedNull(): void
    {
        $target = ['user' => ['name' => 'John', 'age' => 30]];
        $source = ['user' => ['age' => null]];

        $result = DiffMergeStrategies::mergeRfc7396($target, $source);

        $this->assertEquals('John', $result['user']['name']);
        $this->assertArrayNotHasKey('age', $result['user']);
    }

    public function testMergeConflictAware(): void
    {
        $target = ['a' => 1, 'b' => 2];
        $source = ['b' => 3, 'c' => 4];
        $base = ['a' => 1, 'b' => 2];

        $result = DiffMergeStrategies::mergeConflictAware($target, $source, $base);

        $this->assertEquals(3, $result['result']['b']);
        $this->assertEmpty($result['conflicts']);
    }

    public function testMergeConflictAwareDetectsConflict(): void
    {
        $target = ['a' => 1, 'b' => 2];
        $source = ['b' => 3];
        $base = ['a' => 1, 'b' => 2];

        $result = DiffMergeStrategies::mergeConflictAware($target, $source, $base);

        $this->assertEmpty($result['conflicts']);
    }

    public function testDiffStructural(): void
    {
        $source = ['a' => 1, 'b' => 2, 'c' => 3];
        $target = ['b' => 2, 'c' => 4, 'd' => 5];

        $result = DiffMergeStrategies::diffStructural($source, $target);

        $this->assertContains('d', $result['added']);
        $this->assertContains('a', $result['removed']);
        $this->assertContains('c', $result['modified']);
    }

    public function testDiffRfc6902(): void
    {
        $source = ['a' => 1, 'b' => 2];
        $target = ['a' => 1, 'c' => 3];

        $patches = DiffMergeStrategies::diffRfc6902($source, $target);

        $this->assertTrue(count($patches) > 0);
        foreach ($patches as $patch) {
            $this->assertArrayHasKey('op', $patch);
            $this->assertArrayHasKey('path', $patch);
            $this->assertContains($patch['op'], ['add', 'remove', 'replace']);
        }
    }

    public function testDiffDetailed(): void
    {
        $source = ['a' => 1, 'b' => 2];
        $target = ['a' => 1, 'c' => 3];

        $result = DiffMergeStrategies::diffDetailed($source, $target);

        $this->assertArrayHasKey('structural', $result);
        $this->assertArrayHasKey('patches', $result);
        $this->assertArrayHasKey('equality', $result);
        $this->assertArrayHasKey('similarity', $result);
        $this->assertFalse($result['equality']);
    }

    public function testDiffSummary(): void
    {
        $source = ['a' => 1, 'b' => 2, 'c' => 3];
        $target = ['a' => 1, 'c' => 4, 'd' => 5];

        $result = DiffMergeStrategies::diffSummary($source, $target);

        $this->assertEquals(1, $result['added_count']);
        $this->assertEquals(1, $result['removed_count']);
        $this->assertEquals(1, $result['modified_count']);
        $this->assertFalse($result['equal']);
    }

    public function testGetMergeStrategyCallable(): void
    {
        $callable = DiffMergeStrategies::getMergeStrategy(DiffMergeStrategy::MERGE_RECURSIVE);

        $this->assertTrue(is_callable($callable));

        $result = $callable(['a' => 1], ['b' => 2]);

        $this->assertEquals(['a' => 1, 'b' => 2], $result);
    }

    public function testGetDiffStrategyCallable(): void
    {
        $callable = DiffMergeStrategies::getDiffStrategy(DiffMergeStrategy::DIFF_SUMMARY);

        $this->assertTrue(is_callable($callable));

        $result = $callable(['a' => 1], ['b' => 2]);

        $this->assertArrayHasKey('added_count', $result);
    }

    public function testInvalidMergeStrategyThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DiffMergeStrategies::getMergeStrategy(DiffMergeStrategy::DIFF_DETAILED);
    }

    public function testInvalidDiffStrategyThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DiffMergeStrategies::getDiffStrategy(DiffMergeStrategy::MERGE_RECURSIVE);
    }

    public function testEmptySourceAndTarget(): void
    {
        $result = DiffMergeStrategies::mergeRecursive([], []);

        $this->assertEquals([], $result);
    }

    public function testScalarSourceReplacedWithScalarTarget(): void
    {
        $result = DiffMergeStrategies::mergeRecursive('string', ['array' => 'value']);

        $this->assertEquals(['array' => 'value'], $result);
    }
}
