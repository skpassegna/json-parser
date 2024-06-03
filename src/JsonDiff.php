<?php

namespace Skpassegna\JsonParser;

class JsonDiff
{
    public static function diff(JsonObject $object1, JsonObject $object2): array
    {
        $diff = [];

        foreach ($object1 as $key => $value) {
            if (!$object2->has($key)) {
                $diff[] = ['op' => 'remove', 'path' => '/' . $key];
            } elseif ($value instanceof JsonObject && $object2->get($key) instanceof JsonObject) {
                $childDiff = static::diff($value, $object2->get($key));
                foreach ($childDiff as $childDiffOperation) {
                    $diff[] = ['op' => $childDiffOperation['op'], 'path' => '/' . $key . $childDiffOperation['path']];
                }
            } elseif ($value instanceof JsonArray && $object2->get($key) instanceof JsonArray) {
                $childDiff = static::diffArray($value, $object2->get($key));
                foreach ($childDiff as $childDiffOperation) {
                    $diff[] = ['op' => $childDiffOperation['op'], 'path' => '/' . $key . $childDiffOperation['path']];
                }
            } elseif ($value !== $object2->get($key)) {
                $diff[] = ['op' => 'replace', 'path' => '/' . $key, 'value' => $value];
            }
        }

        foreach ($object2 as $key => $value) {
            if (!$object1->has($key)) {
                $diff[] = ['op' => 'add', 'path' => '/' . $key, 'value' => $value];
            }
        }

        return $diff;
    }

    private static function diffArray(JsonArray $array1, JsonArray $array2): array
    {
        $diff = [];
        $length = max($array1->count(), $array2->count());

        for ($i = 0; $i < $length; $i++) {
            if (!$array1->offsetExists($i)) {
                $diff[] = ['op' => 'add', 'path' => '/' . $i, 'value' => $array2[$i]];
            } elseif (!$array2->offsetExists($i)) {
                $diff[] = ['op' => 'remove', 'path' => '/' . $i];
            } elseif ($array1[$i] instanceof JsonObject && $array2[$i] instanceof JsonObject) {
                $childDiff = static::diff($array1[$i], $array2[$i]);
                foreach ($childDiff as $childDiffOperation) {
                    $diff[] = ['op' => $childDiffOperation['op'], 'path' => '/' . $i . $childDiffOperation['path']];
                }
            } elseif ($array1[$i] instanceof JsonArray && $array2[$i] instanceof JsonArray) {
                $childDiff = static::diffArray($array1[$i], $array2[$i]);
                foreach ($childDiff as $childDiffOperation) {
                    $diff[] = ['op' => $childDiffOperation['op'], 'path' => '/' . $i . $childDiffOperation['path']];
                }
            } elseif ($array1[$i] !== $array2[$i]) {
                $diff[] = ['op' => 'replace', 'path' => '/' . $i, 'value' => $array2[$i]];
            }
        }

        return $diff;
    }
}
