<?php

/**
 * Real-World: Email Template Rendering
 *
 * Render email templates with JSON data.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

print_section('Email Template Rendering');

$data = Json::create([
    'recipient' => 'Alice',
    'order_id' => 'ORD-12345',
    'total' => 99.99,
    'items_count' => 3,
]);

$template = "
Hello {name},

Your order #{order_id} has been confirmed.

Items: {count}
Total: \${amount}

Thank you for your purchase!
";

$rendered = $template;
$rendered = str_replace('{name}', $data->get('recipient'), $rendered);
$rendered = str_replace('{order_id}', $data->get('order_id'), $rendered);
$rendered = str_replace('{count}', $data->get('items_count'), $rendered);
$rendered = str_replace('{amount}', number_format($data->get('total'), 2), $rendered);

echo $rendered;

print_section('Examples Complete');
