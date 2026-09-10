<?php

namespace App\Support;

/** Enumerate text leaves without changing the document's curriculum-specific shape. */
class RppContentEditor
{
    public static function fields(array $content, array $path = []): array
    {
        $fields = [];
        foreach ($content as $key => $value) {
            $location = [...$path, $key];
            if (is_array($value)) {
                array_push($fields, ...self::fields($value, $location));
            } elseif (is_string($value)) {
                $fields[] = ['path' => $location, 'label' => implode(' / ', array_map(fn ($part) => is_int($part) ? 'Butir '.($part + 1) : ucfirst(str_replace('_', ' ', $part)), $location)), 'value' => $value];
            }
        }

        return $fields;
    }

    public static function replace(array $content, array $fields, array $edits): array
    {
        foreach ($edits as $index => $text) {
            $leaf = &$content;
            foreach ($fields[$index]['path'] as $part) {
                $leaf = &$leaf[$part];
            }
            $leaf = $text ?? '';
            unset($leaf);
        }

        return $content;
    }
}
