<?php

/**
 * Numeric Type Operations
 *
 * Working with numeric types and calculations.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

print_section('Numeric Operations');

$data = Json::create([
    'price' => 99.99,
    'quantity' => '5',
    'discount' => 0.1,
]);

$price = (float)$data->get('price');
$quantity = (int)$data->get('quantity');
$discount = (float)$data->get('discount');

$subtotal = $price * $quantity;
$total = $subtotal * (1 - $discount);

echo "Price: \$" . number_format($price, 2) . "\n";
echo "Quantity: " . $quantity . "\n";
echo "Discount: " . ($discount * 100) . "%\n";
echo "Subtotal: \$" . number_format($subtotal, 2) . "\n";
echo "Total: \$" . number_format($total, 2) . "\n";

print_section('Examples Complete');
