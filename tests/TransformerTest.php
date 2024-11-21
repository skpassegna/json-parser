<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Json\Transformer;
use Skpassegna\Json\Exceptions\RuntimeException;

class TransformerTest extends TestCase
{
    private Transformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new Transformer();
    }

    public function testToXmlWithValidJson(): void
    {
        $json = '{"name":"John","age":30,"hobbies":["reading","gaming"]}';
        $xml = $this->transformer->toXml($json);
        
        $this->assertIsString($xml);
        $this->assertStringContainsString('<name>John</name>', $xml);
        $this->assertStringContainsString('<age>30</age>', $xml);
        $this->assertStringContainsString('<hobbies>', $xml);
        $this->assertStringContainsString('<item0>reading</item0>', $xml);
        $this->assertStringContainsString('<item1>gaming</item1>', $xml);
    }

    public function testToXmlWithInvalidJson(): void
    {
        $this->expectException(RuntimeException::class);
        $json = '{invalid json}';
        $this->transformer->toXml($json);
    }

    public function testToYamlWithValidJson(): void
    {
        if (!function_exists('yaml_emit')) {
            $this->markTestSkipped('YAML extension not available');
        }

        $json = '{"name":"John","age":30,"hobbies":["reading","gaming"]}';
        $yaml = $this->transformer->toYaml($json);
        
        $this->assertIsString($yaml);
        $this->assertStringContainsString('name: John', $yaml);
        $this->assertStringContainsString('age: 30', $yaml);
        $this->assertStringContainsString('hobbies:', $yaml);
        $this->assertStringContainsString('- reading', $yaml);
        $this->assertStringContainsString('- gaming', $yaml);
    }

    public function testToYamlWithInvalidJson(): void
    {
        if (!function_exists('yaml_emit')) {
            $this->markTestSkipped('YAML extension not available');
        }

        $this->expectException(RuntimeException::class);
        $json = '{invalid json}';
        $this->transformer->toYaml($json);
    }

    public function testToCsvWithValidJson(): void
    {
        $json = '[{"name":"John","age":30},{"name":"Jane","age":25}]';
        $csv = $this->transformer->toCsv($json);
        
        $this->assertIsString($csv);
        $this->assertStringContainsString('name,age', $csv);
        $this->assertStringContainsString('John,30', $csv);
        $this->assertStringContainsString('Jane,25', $csv);
    }

    public function testToCsvWithInvalidJson(): void
    {
        $this->expectException(RuntimeException::class);
        $json = '{invalid json}';
        $this->transformer->toCsv($json);
    }

    public function testToCsvWithInvalidStructure(): void
    {
        $this->expectException(RuntimeException::class);
        $json = '{"name":"John","age":30}'; // Not an array of objects
        $this->transformer->toCsv($json);
    }

    public function testToCsvWithInconsistentStructure(): void
    {
        $this->expectException(RuntimeException::class);
        $json = '[{"name":"John","age":30},{"name":"Jane"}]'; // Missing age in second object
        $this->transformer->toCsv($json);
    }
}
