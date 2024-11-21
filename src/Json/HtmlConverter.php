<?php

declare(strict_types=1);

namespace Skpassegna\Json\Json;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use Skpassegna\Json\Contracts\HtmlConverterInterface;
use Skpassegna\Json\Exceptions\RuntimeException;

/**
 * Converts HTML to JSON with various output formats and options
 */
class HtmlConverter implements HtmlConverterInterface
{
    private Serializer $serializer;
    
    /**
     * @var array<string> Default attributes to include
     */
    private array $defaultAttributes = ['id', 'class', 'href', 'src', 'alt', 'title'];
    
    /**
     * @var array<string> Elements to treat as self-closing
     */
    private array $selfClosingTags = ['img', 'br', 'hr', 'input', 'meta', 'link'];

    public function __construct()
    {
        $this->serializer = new Serializer();
    }

    /**
     * Convert HTML to JSON
     *
     * @param string $html HTML content to convert
     * @param array<string,mixed> $options Conversion options
     * @return string JSON representation of HTML
     * @throws RuntimeException If conversion fails
     */
    public function convert(string $html, array $options = []): string
    {
        try {
            // Configure options with defaults
            $options = array_merge([
                'preserveWhitespace' => false,
                'includeAttributes' => true,
                'customAttributes' => [],
                'excludeTags' => [],
                'format' => 'hierarchical', // 'hierarchical', 'flat', or 'simplified'
                'textNodesKey' => '_text',
                'attributesKey' => '_attributes',
                'childrenKey' => '_children',
                'typeKey' => '_type',
            ], $options);

            // Load HTML
            $dom = new DOMDocument();
            $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR);

            // Convert to array structure
            $result = $this->convertNode($dom->documentElement, $options);

            // Return JSON
            return $this->serializer->serialize($result);
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to convert HTML to JSON: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Convert a DOM node to array structure
     *
     * @param DOMNode $node Node to convert
     * @param array<string,mixed> $options Conversion options
     * @return array<string,mixed> Converted structure
     */
    private function convertNode(DOMNode $node, array $options): array
    {
        if ($node instanceof DOMText) {
            return $this->handleTextNode($node, $options);
        }

        if (!($node instanceof DOMElement)) {
            return [];
        }

        // Skip excluded tags
        if (in_array($node->tagName, $options['excludeTags'])) {
            return [];
        }

        $result = [];

        // Add node type
        if ($options['format'] !== 'simplified') {
            $result[$options['typeKey']] = $node->tagName;
        }

        // Add attributes if enabled
        if ($options['includeAttributes']) {
            $attributes = $this->extractAttributes($node, $options);
            if (!empty($attributes)) {
                $result[$options['attributesKey']] = $attributes;
            }
        }

        // Process child nodes
        $children = [];
        $textContent = '';

        if (!in_array($node->tagName, $this->selfClosingTags)) {
            foreach ($node->childNodes as $childNode) {
                if ($childNode instanceof DOMText) {
                    $text = $this->handleTextNode($childNode, $options);
                    if (!empty($text)) {
                        if ($options['format'] === 'simplified') {
                            $textContent .= $text[$options['textNodesKey']];
                        } else {
                            $children[] = $text;
                        }
                    }
                } elseif ($childNode instanceof DOMElement) {
                    $childResult = $this->convertNode($childNode, $options);
                    if (!empty($childResult)) {
                        $children[] = $childResult;
                    }
                }
            }
        }

        // Handle different output formats
        switch ($options['format']) {
            case 'flat':
                if (!empty($children)) {
                    foreach ($children as $child) {
                        $result[] = $child;
                    }
                }
                break;

            case 'simplified':
                if (!empty($textContent)) {
                    $result[$options['textNodesKey']] = $textContent;
                }
                if (!empty($children)) {
                    foreach ($children as $child) {
                        if (isset($child[$options['typeKey']])) {
                            $type = $child[$options['typeKey']];
                            unset($child[$options['typeKey']]);
                            $result[$type] = $child;
                        }
                    }
                }
                break;

            case 'hierarchical':
            default:
                if (!empty($children)) {
                    $result[$options['childrenKey']] = $children;
                }
                break;
        }

        return $result;
    }

    /**
     * Handle text node conversion
     *
     * @param DOMText $node Text node
     * @param array<string,mixed> $options Conversion options
     * @return array<string,mixed> Converted text node
     */
    private function handleTextNode(DOMText $node, array $options): array
    {
        $text = $options['preserveWhitespace'] ? $node->nodeValue : trim($node->nodeValue);
        
        if ($text === '') {
            return [];
        }

        if ($options['format'] === 'simplified') {
            return [$options['textNodesKey'] => $text];
        }

        return [
            $options['typeKey'] => '#text',
            $options['textNodesKey'] => $text
        ];
    }

    /**
     * Extract attributes from a DOM element
     *
     * @param DOMElement $element Element to extract attributes from
     * @param array<string,mixed> $options Conversion options
     * @return array<string,string> Extracted attributes
     */
    private function extractAttributes(DOMElement $element, array $options): array
    {
        $attributes = [];
        $allowedAttributes = array_merge(
            $this->defaultAttributes,
            $options['customAttributes']
        );

        foreach ($element->attributes as $attribute) {
            if (in_array($attribute->name, $allowedAttributes)) {
                $attributes[$attribute->name] = $attribute->value;
            }
        }

        return $attributes;
    }

    /**
     * Convert JSON back to HTML
     *
     * @param string $json JSON to convert
     * @param array<string,mixed> $options Conversion options
     * @return string HTML representation
     * @throws RuntimeException If conversion fails
     */
    public function toHtml(string $json, array $options = []): string
    {
        try {
            $data = $this->serializer->deserialize($json, true);
            
            $options = array_merge([
                'prettyPrint' => false,
                'textNodesKey' => '_text',
                'attributesKey' => '_attributes',
                'childrenKey' => '_children',
                'typeKey' => '_type',
            ], $options);

            $dom = new DOMDocument();
            $dom->formatOutput = $options['prettyPrint'];

            $this->buildHtmlNode($dom, $data, $options);

            return $dom->saveHTML() ?: '';
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to convert JSON to HTML: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Build HTML nodes from JSON data
     *
     * @param DOMDocument $dom DOM document
     * @param array<string,mixed> $data JSON data
     * @param array<string,mixed> $options Conversion options
     * @return DOMNode Created node
     */
    private function buildHtmlNode(DOMDocument $dom, array $data, array $options): DOMNode
    {
        // Handle text nodes
        if (isset($data[$options['textNodesKey']])) {
            return $dom->createTextNode($data[$options['textNodesKey']]);
        }

        // Get node type
        $type = $data[$options['typeKey']] ?? 'div';

        // Create element
        $element = $dom->createElement($type);

        // Add attributes
        if (isset($data[$options['attributesKey']])) {
            foreach ($data[$options['attributesKey']] as $name => $value) {
                $element->setAttribute($name, (string)$value);
            }
        }

        // Add children
        if (isset($data[$options['childrenKey']])) {
            foreach ($data[$options['childrenKey']] as $child) {
                $childNode = $this->buildHtmlNode($dom, $child, $options);
                $element->appendChild($childNode);
            }
        }

        return $element;
    }
}
