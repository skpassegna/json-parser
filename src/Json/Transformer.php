<?php

declare(strict_types=1);

namespace Skpassegna\Json\Json;

use Skpassegna\Json\Contracts\TransformerInterface;
use Skpassegna\Json\Exceptions\RuntimeException;

class Transformer implements TransformerInterface
{
    private Serializer $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer();
    }

    /**
     * @inheritDoc
     */
    public function toXml(string $json): string
    {
        try {
            $data = $this->serializer->deserialize($json, true);
            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root></root>');
            $this->arrayToXml($data, $xml);
            return $xml->asXML();
        } catch (\Exception $e) {
            throw new RuntimeException(
                sprintf('XML transformation error: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function toYaml(string $json): string
    {
        if (!function_exists('yaml_emit')) {
            throw new RuntimeException('YAML extension is required for YAML transformation');
        }

        try {
            $data = $this->serializer->deserialize($json, true);
            $yaml = yaml_emit($data, YAML_UTF8_ENCODING);
            
            if ($yaml === false) {
                throw new RuntimeException('Failed to transform JSON to YAML');
            }
            
            return $yaml;
        } catch (\Exception $e) {
            throw new RuntimeException(
                sprintf('YAML transformation error: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function toCsv(string $json, string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): string
    {
        try {
            $data = $this->serializer->deserialize($json, true);
            
            if (!is_array($data) || !$this->isValidCsvData($data)) {
                throw new RuntimeException('JSON structure is not suitable for CSV conversion');
            }

            $output = fopen('php://temp', 'r+');
            
            // Write headers
            $headers = array_keys(reset($data));
            fputcsv($output, $headers, $delimiter, $enclosure, $escape);
            
            // Write data rows
            foreach ($data as $row) {
                fputcsv($output, $row, $delimiter, $enclosure, $escape);
            }
            
            rewind($output);
            $csv = stream_get_contents($output);
            fclose($output);
            
            return $csv;
        } catch (\Exception $e) {
            throw new RuntimeException(
                sprintf('CSV transformation error: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Convert array to XML
     *
     * @param array<mixed> $data
     * @param \SimpleXMLElement $xml
     * @return void
     */
    private function arrayToXml(array $data, \SimpleXMLElement $xml): void
    {
        foreach ($data as $key => $value) {
            $key = is_numeric($key) ? 'item' . $key : $key;
            
            if (is_array($value)) {
                $child = $xml->addChild($key);
                $this->arrayToXml($value, $child);
            } else {
                $xml->addChild($key, htmlspecialchars((string)$value));
            }
        }
    }

    /**
     * Check if data structure is valid for CSV conversion
     *
     * @param array<mixed> $data
     * @return bool
     */
    private function isValidCsvData(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        $firstRow = reset($data);
        if (!is_array($firstRow)) {
            return false;
        }

        $keys = array_keys($firstRow);
        foreach ($data as $row) {
            if (!is_array($row) || array_keys($row) !== $keys) {
                return false;
            }
        }

        return true;
    }
}
