<?php

/**
 * Conditional Lazy Loading
 *
 * Load data conditionally based on requirements.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Streaming\LazyJsonProxy;

// Example: Lazy conditional
print_section('Conditional Loading');

$json = '{"user": {"name": "Alice"}, "large_data": "x" * 10000}';
$lazy = new LazyJsonProxy($json);

$userName = $lazy->get('user.name');
echo "User: $userName\n";

print_section('Examples Complete');
