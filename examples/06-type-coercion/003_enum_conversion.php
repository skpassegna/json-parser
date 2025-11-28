<?php

/**
 * Enum and Type Conversion
 *
 * Working with enums and strategic conversions.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Enums\DiffMergeStrategy;

// Example: Map string to enum
print_section('String to Enum Conversion');

$strategyNames = ['MERGE_RECURSIVE', 'MERGE_SHALLOW', 'MERGE_DEEP'];

echo "Available strategies:\n";
foreach ($strategyNames as $name) {
    echo "  - $name\n";
}

print_section('Examples Complete');
