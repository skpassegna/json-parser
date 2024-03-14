<?php

namespace Skpassegna\JsonParser\Exceptions;

use Exception;

class HumanReadableJsonException extends JsonException
{
    /**
     * @var int The JSON error code
     */
    private $errorCode;

    /**
     * HumanReadableJsonException constructor.
     *
     * @param int $errorCode The JSON error code
     * @param string $errorMessage The error message
     * @param int $code The exception code
     * @param Exception|null $previous The previous exception
     */
    public function __construct(int $errorCode, string $errorMessage, int $code = 0, Exception $previous = null)
    {
        $this->errorCode = $errorCode;
        $humanReadableMessage = $this->getHumanReadableMessage($errorCode, $errorMessage);
        parent::__construct($humanReadableMessage, $code, $previous);
    }

    /**
     * Get a human-readable error message based on the JSON error code.
     *
     * @param int $errorCode The JSON error code
     * @param string $errorMessage The original error message
     * @return string The human-readable error message
     */
    private function getHumanReadableMessage(int $errorCode, string $errorMessage): string
    {
        switch ($errorCode) {
            case JSON_ERROR_DEPTH:
                return 'The maximum stack depth has been exceeded. The JSON data is too deeply nested.';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Invalid or malformed JSON data. The parser encountered an unexpected data type.';
            case JSON_ERROR_CTRL_CHAR:
                return 'Invalid or malformed JSON data. The parser encountered an unexpected control character.';
            case JSON_ERROR_SYNTAX:
                return 'Invalid or malformed JSON data. The syntax is incorrect.';
            case JSON_ERROR_UTF8:
                return 'Invalid or malformed JSON data. The input is not valid UTF-8 encoded.';
            default:
                return $errorMessage;
        }
    }
}