<?php

declare(strict_types=1);

namespace Skpassegna\Json\Contracts;

interface TransformerInterface
{
    /**
     * Transform JSON to XML
     *
     * @param string $json The JSON string
     * @return string The XML string
     * @throws \Skpassegna\Json\Exceptions\RuntimeException If transformation fails
     */
    public function toXml(string $json): string;

    /**
     * Transform JSON to YAML
     *
     * @param string $json The JSON string
     * @return string The YAML string
     * @throws \Skpassegna\Json\Exceptions\RuntimeException If transformation fails
     */
    public function toYaml(string $json): string;

    /**
     * Transform JSON to CSV
     *
     * @param string $json The JSON string
     * @param string $delimiter The CSV delimiter
     * @param string $enclosure The CSV enclosure
     * @param string $escape The CSV escape character
     * @return string The CSV string
     * @throws \Skpassegna\Json\Exceptions\RuntimeException If transformation fails
     */
    public function toCsv(string $json, string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): string;
}
