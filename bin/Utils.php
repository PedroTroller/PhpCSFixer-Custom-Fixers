<?php

declare(strict_types=1);

final class Utils
{
    public static function arrayToString(array $array = null)
    {
        if (null === $array) {
            return;
        }

        $string = '[ ';

        if (array_values($array) === $array) {
            $string .= implode(', ', array_map([self::class, 'valueToString'], $array));
        } else {
            $string .= implode(', ', array_map(function ($value, $key) {
                return '"'.$key.'" => '.static::valueToString($value);
            }, $array, array_keys($array)));
        }

        $string .= ' ]';

        return $string;
    }

    private static function valueToString($value = null)
    {
        if (is_string($value)) {
            return sprintf('"%s"', $value);
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value)) {
            return static::arrayToString($value);
        }

        if (is_null($value)) {
            return '~';
        }

        return $value;
    }
}
