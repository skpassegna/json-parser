<?php

/**
 * Complex Workflows with Fluent Interface
 *
 * Demonstrates real workflows combining multiple operations.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example: Build complete order
print_section('Complete Order Processing Workflow');

$order = Json::create()
    ->set('order_id', 'ORD-' . date('YmdHis'))
    ->set('customer.id', 123)
    ->set('customer.name', 'John Doe')
    ->set('customer.email', 'john@example.com')
    ->set('shipping.method', 'express')
    ->set('shipping.cost', 25.00)
    ->set('billing.address', '123 Main St')
    ->set('billing.city', 'NYC')
    ->set('created_at', date('c'))
    ->set('status', 'pending');

// Add items
$order->set('items.0.product_id', 1)
    ->set('items.0.name', 'Widget')
    ->set('items.0.quantity', 2)
    ->set('items.0.price', 50.00);

$order->set('items.1.product_id', 2)
    ->set('items.1.name', 'Gadget')
    ->set('items.1.quantity', 1)
    ->set('items.1.price', 100.00);

// Calculate totals
$subtotal = (2 * 50) + (1 * 100);
$tax = $subtotal * 0.08;
$shipping = 25;
$total = $subtotal + $tax + $shipping;

$order->set('subtotal', $subtotal)
    ->set('tax', $tax)
    ->set('shipping', $shipping)
    ->set('total', $total);

print_json($order);

print_section('Examples Complete');
