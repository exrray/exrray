<?php
namespace Exrray;

class Exrray
{

    public static function &get(array &$array, string $path, mixed $default = null): mixed
    {
        $path = array_filter(explode('.', $path));
        $current = &$array;

        foreach ($path as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                return $default;
            }

            $current = &$current[$key];

            if (is_callable($current)) {
                $current = $current();
            }
        }

        return $current;
    }

    public static function set(array &$array, string $path, mixed $value): void
    {
        $path = array_filter(explode('.', $path));
        $current = &$array;

        foreach ($path as $key) {
            if (!is_array($current)) {
                $current = [];
            }

            $current = &$current[$key];
        }

        $current = $value;
    }

    public static function merge(array &$array1, array &$array2): array
    {
        $merged = &$array1;

        foreach ($array2 as $key => $value) {
            if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key])) {
                $merged[$key] = static::merge($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    public static function mergeTo(string $path, array &$array1, array &$array2): void
    {
        $value = &static::get($array1, $path);

        if (is_array($value)) {
            static::merge($value, $array2);
        } else {
            static::set($array1, $path, $array2);
        }
    }

    public static function walk(array &$array, callable $callback): void
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                static::walk($value, $callback);
            } else {
                $callback($value, $key);
            }
        }
    }

    // public static function reduce(array &$array, callable $callback, mixed $initial = null): mixed
    // {
    //     $accumulator = $initial;

    //     foreach ($array as $key => &$value) {
    //         if (is_array($value)) {
    //             $value = static::reduce($value, $callback, $initial);
    //         }

    //         $accumulator = $callback($accumulator, $value, $key);

    //         unset($array[$key]);
    //     }

    //     return $accumulator;
    // }
}