<?php

declare(strict_types=1);

/**
 * Procedural API - Merge and Diff Operations
 *
 * This example demonstrates merging JSON data and computing differences
 * using the procedural API.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use function Skpassegna\Json\Procedural\{
    json_parse,
    json_merge,
    json_merge_with_strategy,
    json_diff,
    json_diff_with_strategy,
    json_pretty,
};

use Skpassegna\Json\Enums\DiffMergeStrategy;

echo "=== Procedural API - Merge and Diff ===\n\n";

// Example 1: Basic merge
echo "1. Basic recursive merge:\n";
$original = json_parse('{"name": "Alice", "age": 30, "skills": ["PHP", "JavaScript"]}');
$updates = json_parse('{"age": 31, "city": "New York"}');

$merged = json_parse('{"name": "Alice", "age": 30, "skills": ["PHP", "JavaScript"]}');
json_merge($merged, $updates);

echo "   Original: " . json_pretty($original) . "\n";
echo "   Updates: " . json_pretty($updates) . "\n";
echo "   Result: " . json_pretty($merged) . "\n\n";

// Example 2: Shallow merge
echo "2. Shallow merge (top-level only):\n";
$config1 = json_parse('{"db": {"host": "localhost", "port": 3306}}');
$config2 = json_parse('{"db": {"user": "admin"}}');

$result = json_parse('{"db": {"host": "localhost", "port": 3306}}');
json_merge($result, $config2, false);

echo "   Shallow merge result: " . json_pretty($result) . "\n\n";

// Example 3: Merge with strategy (RFC 7396 - JSON Merge Patch)
echo "3. Merge with RFC 7396 strategy (null deletes keys):\n";
$document = json_parse('{"name": "Alice", "age": 30, "email": "alice@example.com"}');
$patch = json_parse('{"email": null}');

$patched = json_parse('{"name": "Alice", "age": 30, "email": "alice@example.com"}');
json_merge_with_strategy($patched, $patch, DiffMergeStrategy::MERGE_PATCH_RFC7396);

echo "   Before: " . json_pretty($document) . "\n";
echo "   Patch: " . json_pretty($patch) . "\n";
echo "   Result (email removed): " . json_pretty($patched) . "\n\n";

// Example 4: Compute diff between two versions
echo "4. Compute difference between two versions:\n";
$v1 = json_parse('{"name": "Alice", "age": 30, "city": "Boston"}');
$v2 = json_parse('{"name": "Alice", "age": 31, "city": "New York", "country": "USA"}');

$diff = json_diff($v1, $v2);

echo "   Version 1: " . json_pretty($v1) . "\n";
echo "   Version 2: " . json_pretty($v2) . "\n";
echo "   Diff structure: " . json_pretty($diff) . "\n\n";

// Example 5: Diff with RFC 6902 strategy (JSON Patch format)
echo "5. Diff with RFC 6902 strategy (JSON Patch format):\n";
$before = json_parse('{"items": [{"id": 1, "name": "Item A"}]}');
$after = json_parse('{"items": [{"id": 1, "name": "Item A"}, {"id": 2, "name": "Item B"}]}');

$patch = json_diff_with_strategy($before, $after, DiffMergeStrategy::DIFF_RFC6902_PATCH);

echo "   Before: " . json_pretty($before) . "\n";
echo "   After: " . json_pretty($after) . "\n";
echo "   RFC 6902 Patch: " . json_pretty($patch) . "\n\n";

echo "All merge/diff examples completed successfully!\n";
