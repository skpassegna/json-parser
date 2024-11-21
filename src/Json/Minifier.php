<?php

declare(strict_types=1);

namespace Skpassegna\Json\Json;

use Skpassegna\Json\Contracts\MinifierInterface;
use Skpassegna\Json\Exceptions\RuntimeException;

/**
 * JSON Minifier/Compressor
 * Reduces JSON size by removing whitespace and optionally applying compression
 */
class Minifier implements MinifierInterface
{
    private Serializer $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer();
    }

    /**
     * Minify a JSON string by removing unnecessary whitespace
     *
     * @param string $json JSON string to minify
     * @return string Minified JSON string
     * @throws RuntimeException If minification fails
     */
    public function minify(string $json): string
    {
        try {
            // First validate and normalize the JSON
            $data = $this->serializer->deserialize($json, true);
            return $this->serializer->serialize($data);
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to minify JSON: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Compress JSON using various compression methods
     *
     * @param string $json JSON string to compress
     * @param string $method Compression method ('gzip', 'bzip2', or 'deflate')
     * @param int $level Compression level (1-9, higher = better compression but slower)
     * @return string Compressed JSON string (base64 encoded)
     * @throws RuntimeException If compression fails
     */
    public function compress(string $json, string $method = 'gzip', int $level = 6): string
    {
        try {
            // First minify the JSON
            $minified = $this->minify($json);

            return match ($method) {
                'gzip' => $this->compressGzip($minified, $level),
                'bzip2' => $this->compressBzip2($minified, $level),
                'deflate' => $this->compressDeflate($minified, $level),
                default => throw new RuntimeException("Unsupported compression method: $method")
            };
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to compress JSON: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Decompress a compressed JSON string
     *
     * @param string $compressed Compressed JSON string (base64 encoded)
     * @param string $method Compression method used
     * @return string Original JSON string
     * @throws RuntimeException If decompression fails
     */
    public function decompress(string $compressed, string $method = 'gzip'): string
    {
        try {
            return match ($method) {
                'gzip' => $this->decompressGzip($compressed),
                'bzip2' => $this->decompressBzip2($compressed),
                'deflate' => $this->decompressDeflate($compressed),
                default => throw new RuntimeException("Unsupported compression method: $method")
            };
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to decompress JSON: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Format JSON string with proper indentation and spacing
     *
     * @param string $json JSON string to format
     * @param int $indent Number of spaces for indentation
     * @return string Formatted JSON string
     * @throws RuntimeException If formatting fails
     */
    public function format(string $json, int $indent = 4): string
    {
        try {
            $data = $this->serializer->deserialize($json, true);
            return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, $indent);
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to format JSON: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Compress using gzip
     *
     * @param string $data Data to compress
     * @param int $level Compression level
     * @return string Compressed data (base64 encoded)
     */
    private function compressGzip(string $data, int $level): string
    {
        if (!function_exists('gzencode')) {
            throw new RuntimeException('Gzip compression not available');
        }

        $compressed = gzencode($data, $level);
        return $compressed === false 
            ? throw new RuntimeException('Gzip compression failed')
            : base64_encode($compressed);
    }

    /**
     * Decompress gzip data
     *
     * @param string $data Compressed data (base64 encoded)
     * @return string Decompressed data
     */
    private function decompressGzip(string $data): string
    {
        if (!function_exists('gzdecode')) {
            throw new RuntimeException('Gzip decompression not available');
        }

        $decoded = base64_decode($data, true);
        if ($decoded === false) {
            throw new RuntimeException('Invalid base64 encoding');
        }

        $decompressed = gzdecode($decoded);
        return $decompressed === false
            ? throw new RuntimeException('Gzip decompression failed')
            : $decompressed;
    }

    /**
     * Compress using bzip2
     *
     * @param string $data Data to compress
     * @param int $level Compression level
     * @return string Compressed data (base64 encoded)
     */
    private function compressBzip2(string $data, int $level): string
    {
        if (!function_exists('bzcompress')) {
            throw new RuntimeException('Bzip2 compression not available');
        }

        $compressed = bzcompress($data, $level);
        return $compressed === false
            ? throw new RuntimeException('Bzip2 compression failed')
            : base64_encode($compressed);
    }

    /**
     * Decompress bzip2 data
     *
     * @param string $data Compressed data (base64 encoded)
     * @return string Decompressed data
     */
    private function decompressBzip2(string $data): string
    {
        if (!function_exists('bzdecompress')) {
            throw new RuntimeException('Bzip2 decompression not available');
        }

        $decoded = base64_decode($data, true);
        if ($decoded === false) {
            throw new RuntimeException('Invalid base64 encoding');
        }

        $decompressed = bzdecompress($decoded);
        return $decompressed === false
            ? throw new RuntimeException('Bzip2 decompression failed')
            : $decompressed;
    }

    /**
     * Compress using deflate
     *
     * @param string $data Data to compress
     * @param int $level Compression level
     * @return string Compressed data (base64 encoded)
     */
    private function compressDeflate(string $data, int $level): string
    {
        if (!function_exists('gzdeflate')) {
            throw new RuntimeException('Deflate compression not available');
        }

        $compressed = gzdeflate($data, $level);
        return $compressed === false
            ? throw new RuntimeException('Deflate compression failed')
            : base64_encode($compressed);
    }

    /**
     * Decompress deflate data
     *
     * @param string $data Compressed data (base64 encoded)
     * @return string Decompressed data
     */
    private function decompressDeflate(string $data): string
    {
        if (!function_exists('gzinflate')) {
            throw new RuntimeException('Deflate decompression not available');
        }

        $decoded = base64_decode($data, true);
        if ($decoded === false) {
            throw new RuntimeException('Invalid base64 encoding');
        }

        $decompressed = gzinflate($decoded);
        return $decompressed === false
            ? throw new RuntimeException('Deflate decompression failed')
            : $decompressed;
    }
}
