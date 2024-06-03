<?php

namespace Skpassegna\JsonParser;

use Skpassegna\JsonParser\Exceptions\InvalidJsonPatchException;

class JsonPatch
{
    public static function apply(JsonObject $object, array $patch): JsonObject
    {
        $modifiedObject = clone $object;

        foreach ($patch as $operation) {
            if (!isset($operation['op']) || !in_array($operation['op'], ['add', 'remove', 'replace'])) {
                throw new InvalidJsonPatchException('Invalid JSON Patch operation.');
            }

            if (!isset($operation['path']) || !preg_match('/^\/((?:[^\/]+?(?=\/))?)+$/', $operation['path'])) {
                throw new InvalidJsonPatchException('Invalid JSON Patch path.');
            }

            $path = array_filter(explode('/', trim($operation['path'], '/')));
            $target = &$modifiedObject;

            foreach ($path as $step) {
                if ($target instanceof JsonArray) {
                    if (!is_numeric($step) || !$target->offsetExists($step)) {
                        throw new InvalidJsonPatchException('Invalid JSON Patch path.');
                    }
                    $target = &$target->offsetGet($step);
                } elseif ($target instanceof JsonObject) {
                    if (!$target->has($step)) {
                        if ($operation['op'] === 'remove') {
                            continue 2; // Skip this operation
                        }
                        $target->set($step, null);
                    }
                    $target = &$target->get($step);
                } else {
                    throw new InvalidJsonPatchException('Invalid JSON Patch path.');
                }
            }

            switch ($operation['op']) {
                case 'add':
                    if (isset($operation['value'])) {
                        $target = $operation['value'];
                    } else {
                        throw new InvalidJsonPatchException('Missing value for "add" operation.');
                    }
                    break;
                case 'remove':
                    $parent = &$modifiedObject;
                    $key = array_pop($path);
                    if ($parent instanceof JsonObject) {
                        $parent->remove($key);
                    } elseif ($parent instanceof JsonArray) {
                        $parent->offsetUnset($key);
                    }
                    break;
                case 'replace':
                    if (isset($operation['value'])) {
                        $target = $operation['value'];
                    } else {
                        throw new InvalidJsonPatchException('Missing value for "replace" operation.');
                    }
                    break;
            }
        }

        return $modifiedObject;
    }
}