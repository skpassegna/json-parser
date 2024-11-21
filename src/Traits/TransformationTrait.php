<?php

declare(strict_types=1);

namespace Skpassegna\Json\Traits;

use DOMDocument;
use SimpleXMLElement;
use Skpassegna\Json\Exceptions\TransformException;

trait TransformationTrait
{
    /**
     * Convert JSON to XML.
     *
     * @param string $rootElement
     * @return string
     * @throws TransformException
     */
    public function toXml(string $rootElement = 'root'): string
    {
        try {
            $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><{$rootElement}></{$rootElement}>");
            $this->arrayToXml($this->data, $xml);

            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($xml->asXML());

            return $dom->saveXML();
        } catch (\Exception $e) {
            throw new TransformException("Failed to convert JSON to XML: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * Convert JSON to YAML.
     *
     * @param int $indent
     * @return string
     * @throws TransformException
     */
    public function toYaml(int $indent = 2): string
    {
        if (!extension_loaded('yaml')) {
            throw new TransformException('YAML extension is not installed');
        }

        try {
            return yaml_emit($this->data, YAML_UTF8_ENCODING, YAML_ANY_BREAK);
        } catch (\Exception $e) {
            throw new TransformException("Failed to convert JSON to YAML: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * Convert JSON to CSV.
     *
     * @param array $headers
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @return string
     * @throws TransformException
     */
    public function toCsv(
        array $headers = [],
        string $delimiter = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ): string {
        if (!is_array($this->data)) {
            throw new TransformException('JSON data must be an array to convert to CSV');
        }

        try {
            $output = fopen('php://temp', 'r+');
            
            // If headers are not provided, use the keys of the first row
            if (empty($headers) && !empty($this->data)) {
                $firstRow = reset($this->data);
                if (is_array($firstRow)) {
                    $headers = array_keys($firstRow);
                }
            }

            // Write headers
            if (!empty($headers)) {
                fputcsv($output, $headers, $delimiter, $enclosure, $escape);
            }

            // Write data
            foreach ($this->data as $row) {
                if (!is_array($row)) {
                    $row = [$row];
                }
                
                if (!empty($headers)) {
                    $orderedRow = [];
                    foreach ($headers as $header) {
                        $orderedRow[] = $row[$header] ?? '';
                    }
                    $row = $orderedRow;
                }
                
                fputcsv($output, $row, $delimiter, $enclosure, $escape);
            }

            rewind($output);
            $csv = stream_get_contents($output);
            fclose($output);

            return $csv;
        } catch (\Exception $e) {
            throw new TransformException("Failed to convert JSON to CSV: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * Convert array to XML helper.
     *
     * @param array|string|int|float|bool|null $data
     * @param SimpleXMLElement $xml
     * @return void
     */
    private function arrayToXml(mixed $data, SimpleXMLElement &$xml): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item' . $key;
                }
                $subnode = $xml->addChild($key);
                $this->arrayToXml($value, $subnode);
            } else {
                if (is_numeric($key)) {
                    $key = 'item' . $key;
                }
                $xml->addChild($key, htmlspecialchars((string)$value));
            }
        }
    }

    /**
     * Pretty print JSON.
     *
     * @param int $options
     * @return string
     */
    public function prettyPrint(int $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE): string
    {
        return $this->toString($options);
    }

    /**
     * Minify JSON.
     *
     * @return string
     */
    public function minify(): string
    {
        return $this->toString(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Transform JSON using a callback.
     *
     * @param callable $callback
     * @return static
     */
    public function transform(callable $callback): static
    {
        $new = clone $this;
        $new->data = $callback($this->data);
        return $new;
    }

    /**
     * Flatten a nested JSON structure.
     *
     * @param string $delimiter
     * @return static
     */
    public function flatten(string $delimiter = '.'): static
    {
        $new = clone $this;
        $new->data = $this->flattenArray($this->data, '', $delimiter);
        return $new;
    }

    /**
     * Flatten array helper.
     *
     * @param array $data
     * @param string $prefix
     * @param string $delimiter
     * @return array
     */
    private function flattenArray(array $data, string $prefix = '', string $delimiter = '.'): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $newKey = $prefix ? $prefix . $delimiter . $key : $key;

            if (is_array($value) && !empty($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey, $delimiter));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Unflatten a flattened JSON structure.
     *
     * @param string $delimiter
     * @return static
     */
    public function unflatten(string $delimiter = '.'): static
    {
        if (!is_array($this->data)) {
            return clone $this;
        }

        $new = clone $this;
        $new->data = $this->unflattenArray($this->data, $delimiter);
        return $new;
    }

    /**
     * Unflatten array helper.
     *
     * @param array $data
     * @param string $delimiter
     * @return array
     */
    private function unflattenArray(array $data, string $delimiter = '.'): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $keys = explode($delimiter, $key);
            $current = &$result;

            foreach ($keys as $i => $k) {
                if ($i === count($keys) - 1) {
                    $current[$k] = $value;
                } else {
                    if (!isset($current[$k]) || !is_array($current[$k])) {
                        $current[$k] = [];
                    }
                    $current = &$current[$k];
                }
            }
        }

        return $result;
    }
}
