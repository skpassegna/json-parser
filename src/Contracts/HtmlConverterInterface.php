<?php

declare(strict_types=1);

namespace Skpassegna\Json\Contracts;

interface HtmlConverterInterface
{
    /**
     * Convert HTML to JSON
     *
     * @param string $html HTML content to convert
     * @param array<string,mixed> $options Conversion options
     * @return string JSON representation of HTML
     */
    public function convert(string $html, array $options = []): string;

    /**
     * Convert JSON back to HTML
     *
     * @param string $json JSON to convert
     * @param array<string,mixed> $options Conversion options
     * @return string HTML representation
     */
    public function toHtml(string $json, array $options = []): string;
}
