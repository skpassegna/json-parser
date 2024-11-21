<?php

declare(strict_types=1);

namespace Skpassegna\Json\Utils;

use Symfony\Component\DomCrawler\Crawler;
use Skpassegna\Json\Exceptions\TransformException;

class HtmlConverter
{
    /**
     * Convert HTML to a JSON-compatible array structure.
     *
     * @param string $html HTML content to convert
     * @param array $options Conversion options
     * @return array
     * @throws TransformException
     */
    public static function toArray(string $html, array $options = []): array
    {
        try {
            $crawler = new Crawler($html);
            return self::parseNode($crawler, $options);
        } catch (\Exception $e) {
            throw new TransformException("Failed to convert HTML to array: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * Parse a DOM node and its children recursively.
     *
     * @param Crawler $node
     * @param array $options
     * @return array
     */
    private static function parseNode(Crawler $node, array $options = []): array
    {
        $result = [
            'type' => 'element',
            'name' => $node->nodeName(),
        ];

        // Extract attributes
        if ($node->count() && $node->getNode(0)->hasAttributes()) {
            $result['attributes'] = [];
            foreach ($node->getNode(0)->attributes as $attr) {
                $result['attributes'][$attr->name] = $attr->value;
            }
        }

        // Get text content and handle whitespace based on options
        $text = $options['preserveWhitespace'] ?? false
            ? $node->text()
            : trim($node->text());

        if ($text !== '') {
            $result['text'] = $text;
        }

        // Process children
        $children = [];
        $node->children()->each(function (Crawler $child) use (&$children, $options) {
            $tagName = $child->nodeName();
            
            // Skip excluded tags
            if (!empty($options['excludeTags']) && in_array($tagName, $options['excludeTags'], true)) {
                return;
            }

            // Handle specific tag transformations
            if (!empty($options['transformTags'][$tagName])) {
                $transformer = $options['transformTags'][$tagName];
                $children[] = $transformer($child);
                return;
            }

            $childData = self::parseNode($child, $options);
            if (!empty($childData)) {
                $children[] = $childData;
            }
        });

        if (!empty($children)) {
            $result['children'] = $children;
        }

        return $result;
    }

    /**
     * Convert HTML table to a more structured array format.
     *
     * @param Crawler $table
     * @return array
     */
    public static function tableToArray(Crawler $table): array
    {
        $headers = [];
        $rows = [];

        // Extract headers
        $table->filter('thead th, thead td')->each(function (Crawler $header) use (&$headers) {
            $headers[] = trim($header->text());
        });

        // If no headers found in thead, try first tr
        if (empty($headers)) {
            $table->filter('tr')->first()->filter('th, td')->each(function (Crawler $header) use (&$headers) {
                $headers[] = trim($header->text());
            });
        }

        // Extract rows
        $table->filter('tbody tr, tr')->each(function (Crawler $row) use (&$rows, $headers) {
            $rowData = [];
            $row->filter('td')->each(function (Crawler $cell, $index) use (&$rowData, $headers) {
                $key = $headers[$index] ?? $index;
                $rowData[$key] = trim($cell->text());
            });
            if (!empty($rowData)) {
                $rows[] = $rowData;
            }
        });

        return [
            'headers' => $headers,
            'rows' => $rows
        ];
    }
}
