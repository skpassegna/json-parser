<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Json;
use Skpassegna\Json\Enums\DiffMergeStrategy;
use Skpassegna\Json\Exceptions\ParseException;
use Skpassegna\Json\Exceptions\ValidationException;
use function Skpassegna\Json\Procedural\{
    json_parse,
    json_create,
    json_get,
    json_set,
    json_remove,
    json_has,
    json_query,
    json_merge,
    json_merge_with_strategy,
    json_diff,
    json_diff_with_strategy,
    json_validate,
    json_stringify,
    json_pretty,
    json_data,
    json_count,
    json_is_empty,
    json_to_xml,
    json_to_yaml,
    json_to_csv,
    json_flatten,
    json_unflatten,
};

class ProceduralApiTest extends TestCase
{
    public function test_json_parse_with_valid_json(): void
    {
        $json = json_parse('{"name": "Alice", "age": 30}');
        
        self::assertInstanceOf(Json::class, $json);
        self::assertEquals('Alice', $json->get('name'));
        self::assertEquals(30, $json->get('age'));
    }

    public function test_json_parse_with_invalid_json_throws(): void
    {
        self::expectException(ParseException::class);
        json_parse('{invalid json}');
    }

    public function test_json_create_creates_empty_instance(): void
    {
        $json = json_create();
        
        self::assertInstanceOf(Json::class, $json);
        self::assertTrue($json->isEmpty());
    }

    public function test_json_create_with_data(): void
    {
        $json = json_create(['name' => 'Bob']);
        
        self::assertEquals('Bob', $json->get('name'));
    }

    public function test_json_get_from_json_instance(): void
    {
        $json = json_parse('{"user": {"name": "Alice"}}');
        $name = json_get($json, 'user.name');
        
        self::assertEquals('Alice', $name);
    }

    public function test_json_get_from_array(): void
    {
        $array = ['user' => ['name' => 'Alice']];
        $name = json_get($array, 'user.name');
        
        self::assertEquals('Alice', $name);
    }

    public function test_json_get_with_default(): void
    {
        $json = json_parse('{"name": "Alice"}');
        $missing = json_get($json, 'missing.path', 'default');
        
        self::assertEquals('default', $missing);
    }

    public function test_json_set_on_instance(): void
    {
        $json = json_parse('{"name": "Alice"}');
        json_set($json, 'age', 30);
        
        self::assertEquals(30, $json->get('age'));
    }

    public function test_json_set_nested_path(): void
    {
        $json = json_create();
        json_set($json, 'user.profile.name', 'Alice');
        
        self::assertEquals('Alice', $json->get('user.profile.name'));
    }

    public function test_json_set_returns_instance_for_chaining(): void
    {
        $json = json_create();
        $result = json_set($json, 'name', 'Alice');
        
        self::assertSame($json, $result);
    }

    public function test_json_remove_deletes_path(): void
    {
        $json = json_parse('{"name": "Alice", "age": 30}');
        json_remove($json, 'age');
        
        self::assertFalse($json->has('age'));
        self::assertTrue($json->has('name'));
    }

    public function test_json_has_returns_true_for_existing_path(): void
    {
        $json = json_parse('{"name": "Alice"}');
        
        self::assertTrue(json_has($json, 'name'));
    }

    public function test_json_has_returns_false_for_missing_path(): void
    {
        $json = json_parse('{"name": "Alice"}');
        
        self::assertFalse(json_has($json, 'age'));
    }

    public function test_json_has_with_array(): void
    {
        $array = ['name' => 'Alice'];
        
        self::assertTrue(json_has($array, 'name'));
        self::assertFalse(json_has($array, 'missing'));
    }

    public function test_json_query_with_jsonpath(): void
    {
        $json = json_parse('{"users": [{"name": "Alice"}, {"name": "Bob"}]}');
        $results = json_query($json, '$.users[*].name');
        
        self::assertCount(2, $results);
        self::assertContains('Alice', $results);
        self::assertContains('Bob', $results);
    }

    public function test_json_merge_recursive(): void
    {
        $json = json_parse('{"name": "Alice", "skills": ["PHP"]}');
        json_merge($json, ['age' => 30, 'skills' => ['JavaScript']]);
        
        self::assertEquals(30, $json->get('age'));
        $skills = $json->get('skills');
        self::assertTrue(is_array($skills) || is_object($skills));
    }

    public function test_json_merge_shallow(): void
    {
        $json = json_parse('{"config": ["a", "b"]}');
        json_merge($json, ['config' => ['c']], false);
        
        self::assertEquals(['c'], $json->get('config'));
    }

    public function test_json_merge_with_strategy_rfc7396(): void
    {
        $json = json_parse('{"name": "Alice", "email": "alice@example.com"}');
        json_merge_with_strategy($json, ['email' => null], DiffMergeStrategy::MERGE_PATCH_RFC7396);
        
        self::assertFalse($json->has('email'));
        self::assertEquals('Alice', $json->get('name'));
    }

    public function test_json_diff_returns_differences(): void
    {
        $original = json_parse('{"name": "Alice", "age": 30}');
        $modified = json_parse('{"name": "Alice", "age": 31, "city": "NY"}');
        
        $diff = json_diff($original, $modified);
        
        self::assertIsArray($diff);
    }

    public function test_json_diff_with_strategy_rfc6902(): void
    {
        $original = json_parse('{"items": [{"id": 1}]}');
        $modified = json_parse('{"items": [{"id": 1}, {"id": 2}]}');
        
        $patch = json_diff_with_strategy($original, $modified, DiffMergeStrategy::DIFF_RFC6902_PATCH);
        
        self::assertIsArray($patch);
    }

    public function test_json_validate_with_valid_schema(): void
    {
        $json = json_parse('{"name": "Alice"}');
        $schema = ['type' => 'object', 'properties' => ['name' => ['type' => 'string']]];
        
        self::assertTrue(json_validate($json, $schema));
    }

    public function test_json_stringify(): void
    {
        $json = json_parse('{"name":"Alice","age":30}');
        $string = json_stringify($json);
        
        self::assertIsString($string);
        self::assertStringContainsString('Alice', $string);
        self::assertStringContainsString('30', $string);
    }

    public function test_json_pretty(): void
    {
        $json = json_create(['name' => 'Alice', 'age' => 30]);
        $pretty = json_pretty($json);
        
        self::assertIsString($pretty);
        self::assertStringContainsString('Alice', $pretty);
        self::assertStringContainsString("\n", $pretty); // Pretty print includes newlines
    }

    public function test_json_data_from_instance(): void
    {
        $json = json_parse('{"name": "Alice"}');
        $data = json_data($json);
        
        self::assertIsArray($data);
        self::assertEquals('Alice', $data['name']);
    }

    public function test_json_data_from_array(): void
    {
        $array = ['name' => 'Alice'];
        $data = json_data($array);
        
        self::assertSame($array, $data);
    }

    public function test_json_count(): void
    {
        $json = json_parse('{"a": 1, "b": 2, "c": 3}');
        
        self::assertEquals(3, json_count($json));
    }

    public function test_json_is_empty_returns_true_for_empty(): void
    {
        $json = json_create();
        
        self::assertTrue(json_is_empty($json));
    }

    public function test_json_is_empty_returns_false_for_non_empty(): void
    {
        $json = json_parse('{"name": "Alice"}');
        
        self::assertFalse(json_is_empty($json));
    }

    public function test_json_to_xml(): void
    {
        $json = json_parse('{"root": {"name": "Alice"}}');
        $xml = json_to_xml($json);
        
        self::assertIsString($xml);
        self::assertStringContainsString('<?xml', $xml);
    }

    public function test_json_to_yaml(): void
    {
        $json = json_parse('{"name": "Alice", "age": 30}');
        $yaml = json_to_yaml($json);
        
        self::assertIsString($yaml);
        self::assertStringContainsString('Alice', $yaml);
    }

    public function test_json_to_csv(): void
    {
        $json = json_parse('[{"name": "Alice", "age": 30}, {"name": "Bob", "age": 25}]');
        $csv = json_to_csv($json);
        
        self::assertIsString($csv);
        self::assertStringContainsString('Alice', $csv);
    }

    public function test_json_flatten(): void
    {
        $json = json_parse('{"user": {"profile": {"name": "Alice"}}}');
        $flat = json_flatten($json);
        
        self::assertIsArray($flat);
        self::assertArrayHasKey('user.profile.name', $flat);
        self::assertEquals('Alice', $flat['user.profile.name']);
    }

    public function test_json_unflatten(): void
    {
        $flat = ['user.profile.name' => 'Alice', 'user.profile.age' => 30];
        $unflat = json_unflatten($flat);
        
        self::assertIsArray($unflat);
        self::assertEquals('Alice', $unflat['user']['profile']['name']);
        self::assertEquals(30, $unflat['user']['profile']['age']);
    }

    public function test_procedural_and_oop_produce_same_results(): void
    {
        $jsonString = '{"name": "Alice", "age": 30}';
        
        // OOP approach
        $oop = Json::parse($jsonString);
        $oop->set('city', 'New York');
        $oopResult = $oop->toString();
        
        // Procedural approach
        $procedural = json_parse($jsonString);
        json_set($procedural, 'city', 'New York');
        $proceduralResult = json_stringify($procedural);
        
        // Results should be equivalent
        self::assertEquals(json_decode($oopResult, true), json_decode($proceduralResult, true));
    }

    public function test_procedural_functions_with_plain_arrays(): void
    {
        // Procedural API should work directly with plain arrays
        $array = ['name' => 'Alice', 'age' => 30];
        
        $name = json_get($array, 'name');
        self::assertEquals('Alice', $name);
        
        $exists = json_has($array, 'name');
        self::assertTrue($exists);
        
        $count = json_count($array);
        self::assertEquals(2, $count);
    }
}
