<?php

declare(strict_types=1);

namespace Skpassegna\Json\Utils;

use ColinODell\Json5\Json5;
use Skpassegna\Json\Exceptions\TransformException;

class Json5Handler
{
    /**
     * Decode a JSON5 string to PHP array/object.
     *
     * @param string $json5 The JSON5 string to decode
     * @param bool $assoc When true, returns array instead of object
     * @param int $depth Maximum nesting depth
     * @param int $flags Bitmask of JSON decode options
     * @return mixed
     * @throws TransformException
     */
    public static function decode(
        string $json5,
        bool $assoc = true,
        int $depth = 512,
        int $flags = 0
    ): mixed {
        try {
            return Json5::decode($json5, $assoc, $depth, $flags);
        } catch (\Exception $e) {
            throw new TransformException("Failed to decode JSON5: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * Encode data to JSON5 string.
     *
     * @param mixed $data The data to encode
     * @param int $options JSON encode options
     * @param int $depth Maximum nesting depth
     * @return string
     * @throws TransformException
     */
    public static function encode(
        mixed $data,
        int $options = 0,
        int $depth = 512
    ): string {
        try {
            return Json5::encode($data, $options, $depth);
        } catch (\Exception $e) {
            throw new TransformException("Failed to encode to JSON5: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * Check if a string is valid JSON5.
     *
     * @param string $json5 The string to check
     * @return bool
     */
    public static function isValid(string $json5): bool
    {
        try {
            Json5::decode($json5);
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Parse JSON5 comments.
     *
     * @param string $json5 The JSON5 string to parse
     * @return array Array of comments with their positions
     */
    public static function extractComments(string $json5): array
    {
        $comments = [];
        $lines = explode("\n", $json5);
        $inMultilineComment = false;
        $currentComment = '';

        foreach ($lines as $lineNum => $line) {
            $trimmedLine = trim($line);

            // Handle multiline comments
            if ($inMultilineComment) {
                if (strpos($trimmedLine, '*/') !== false) {
                    $currentComment .= substr($trimmedLine, 0, strpos($trimmedLine, '*/'));
                    $comments[] = [
                        'type' => 'multiline',
                        'content' => $currentComment,
                        'line' => $lineNum + 1
                    ];
                    $inMultilineComment = false;
                    $currentComment = '';
                } else {
                    $currentComment .= $trimmedLine . "\n";
                }
                continue;
            }

            // Detect single-line comments
            if (preg_match('/^\s*\/\/(.*)$/', $trimmedLine, $matches)) {
                $comments[] = [
                    'type' => 'single',
                    'content' => trim($matches[1]),
                    'line' => $lineNum + 1
                ];
            }

            // Detect start of multiline comments
            if (strpos($trimmedLine, '/*') !== false) {
                $inMultilineComment = true;
                $currentComment = substr($trimmedLine, strpos($trimmedLine, '/*') + 2) . "\n";
            }
        }

        return $comments;
    }
}
