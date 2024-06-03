<?php

namespace Skpassegna\JsonParser;

use Skpassegna\JsonParser\Exceptions\JsonStreamException;

class JsonStream
{
    private $stream;

    public function __construct(string $jsonData)
    {
        $this->stream = fopen('php://memory', 'r+');
        fwrite($this->stream, $jsonData);
        rewind($this->stream);
    }

    public function getNextValue(): JsonValue
    {
        // Implement JSON streaming logic here
        // You can use a third-party library like salsify/json-streaming-parser
        // or implement your own JSON streaming parser

        if ($streamError) {
            throw new JsonStreamException('An error occurred while streaming JSON data.');
        }

        return $value;
    }

    public function __destruct()
    {
        fclose($this->stream);
    }
}