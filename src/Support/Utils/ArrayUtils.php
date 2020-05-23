<?php


namespace Commune\Support\Utils;


class ArrayUtils
{

    public static function slice(array &$arr, int $maxLength) : void
    {
        $size = count($arr);
        $maxLength = $maxLength > 0 ? $maxLength : 0;

        while ($size > $maxLength) {
            array_pop($arr);
        }
    }

    /**
     * @param mixed $data
     * @return mixed|array
     */
    public static function recursiveToArray($data)
    {
        if (is_object($data) && method_exists($data, 'toArray')) {
            return $data->toArray();
        }

        if (is_iterable($data)) {
            $results = [];
            foreach ($data as $key => $value) {
                $results[$key] = static::recursiveToArray($value);
            }
            return $results;

        }

        return $data;
    }


    public static function fieldsAreRequired(array $fields, array $data) : ? string
    {
        foreach ($fields as $field) {
            $val = $data[$field] ?? null;
            if (is_null($val) || $val === '') {
                return $field;
            }
        }

        return null;
    }

    public static function expectTokens(
        array $tokens,
        array $expects,
        bool $all = true
    ) : bool
    {
        $tokenMap = array_fill_keys($tokens, true);
        return self::expectTokenMap($tokenMap, $expects, $all);
    }

    public static function expectTokenMap(
        array $tokenMap,
        array $expects,
        bool $all
    ) : bool
    {
        if (empty($expects)) {
            return $all;
        }

        foreach($expects as $expect) {
            $matched = false;

            if (is_array($expect)) {
                $matched = self::expectTokenMap($tokenMap, $expect, !$all);

            } elseif (array_key_exists($expect, $tokenMap)) {
                $matched = true;

            }

            // 任意一个为真
            if (!$all && $matched) {
                return true;
            }

            // 任意一个为假
            if ($all && !$matched) {
                return false;
            }
        }

        // 全部
        return $all;
    }

    public static function recursiveArrayParse(
        array $array,
        callable $parser
    ) : array
    {
        if (empty($array)) {
            return $array;
        }

        return array_map(function($value) use ($parser){

            if (is_array($value)) {
                $value = self::recursiveArrayParse($value, $parser);
            } else {
                $value = $parser($value);
            }

            return $value;

        }, $array);
    }
}