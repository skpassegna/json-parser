<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Skpassegna\Json\Json;

abstract class TestCase extends BaseTestCase
{
    protected function getFixturesPath(string $filename = ''): string
    {
        return __DIR__ . '/Fixtures/' . ltrim($filename, '/');
    }

    protected function loadFixture(string $filename): string
    {
        return file_get_contents($this->getFixturesPath($filename));
    }

    protected function assertJsonEquals($expected, $actual, string $message = ''): void
    {
        if (is_string($expected)) {
            $expected = json_decode($expected, true);
        }
        if (is_string($actual)) {
            $actual = json_decode($actual, true);
        }

        $this->assertEquals($expected, $actual, $message);
    }

    protected function createJson(mixed $data): Json
    {
        return new Json($data);
    }

    protected function assertValidJson(string $json): void
    {
        json_decode($json);
        $this->assertJson($json);
        $this->assertJsonLastError();
    }

    protected function assertInvalidJson(string $json): void
    {
        json_decode($json);
        $this->assertNotEquals(JSON_ERROR_NONE, json_last_error());
    }

    protected function assertJsonLastError(): void
    {
        $this->assertEquals(
            JSON_ERROR_NONE,
            json_last_error(),
            'JSON error: ' . json_last_error_msg()
        );
    }

    protected function getTestFilePath(string $filename): string
    {
        $dir = sys_get_temp_dir() . '/json-parser-tests';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir . '/' . $filename;
    }

    protected function cleanTestFiles(): void
    {
        $dir = sys_get_temp_dir() . '/json-parser-tests';
        if (is_dir($dir)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($dir);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanTestFiles();
    }

    protected function tearDown(): void
    {
        $this->cleanTestFiles();
        parent::tearDown();
    }
}
