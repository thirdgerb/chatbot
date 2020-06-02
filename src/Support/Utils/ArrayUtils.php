<?php


namespace Commune\Support\Utils;


use Commune\Support\Arr\ArrayAndJsonAble;
use Illuminate\Support\Arr;

class ArrayUtils
{

    public static function wrap($data) : array
    {
        if (is_null($data)) {
            return [];
        }

        if (is_array($data)) {
            return $data;
        }

        if ($data instanceof ArrayAndJsonAble) {
            return $data->toArray();
        }

        if (is_object($data) && method_exists($data, 'toArray')) {
            return $data->toArray();
        }

        return [$data];
    }

    public static function fetchArray(array $data, $key)  : array
    {
        if (!isset($data[$key])) {
            return [];
        }

        $value = $data[$key];
        return static::wrap(static::recursiveToArray($value));
    }

    /**
     * @param array $arr
     * @param int $maxLength
     */
    public static function maxLength(array &$arr, int $maxLength) : void
    {
        $arr = array_slice($arr, 0, $maxLength);
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


    /**
     * 根据键名的定义来过滤一个数组. 拥有 [] 标记的键名, 默认接受数组. 否则是非数组. 自动转化.
     * @param array $values
     * @param array $keys
     * @param bool $onlyDefined
     * @return array
     */
    public static function parseValuesByKeysWithListMark(
        array $values,
        array $keys,
        bool $onlyDefined = true
    ) : array
    {
        $results = [];

        foreach ($keys as $key) {

            $isList = TypeUtils::isListTypeHint($key);
            $key = $isList ? TypeUtils::pureListTypeHint($key) : $key;

            // 如果要求数组, 按数组的方式进行封装.
            if ($isList) {
                $results[$key] = isset($values[$key])
                    ? static::wrap($values[$key])
                    : [];

                // 如果不是数组, 即便输入值是数组也只使用第一个参数
            } else {

                $results[$key] = isset($values[$key]) && is_array($values[$key])
                    ? current($values[$key])
                    : ($values[$key] ?? null);
            }
        }

        if ($onlyDefined) {
            return $results;
        }
        return $results + $values;
    }


    /**
     * 检查一个列表, 给出所有的 unique 值.
     * 如果有重复值 name , 则该值变成 name[]
     *
     * @param array $names
     * @return array
     */
    public static function uniqueValuesWithListMark(array $names) : array
    {
        $map = static::valueCount($names);
        return static::uniqueValuesWithListMarkByValueCounts($map);
    }

    public static function uniqueValuesWithListMarkByValueCounts(array $valueCounts) : array
    {
        $result = [];
        foreach ($valueCounts as $name => $count) {
            $result[] = $count > 1 ? $name . '[]' : strval($name);
        }

        return $result;
    }

    /**
     * @param array $array
     * @return array
     */
    public static function valueCount(array $array) : array
    {
        return array_reduce($array, function($map, $name) {
            $map[$name] = $map[$name] ?? 0;
            $map[$name] ++ ;
            return $map;
        }, []);
    }

    public static function mergeMapByMaxVal(array $map, array $mergeMap) : array
    {
        foreach ($mergeMap as $key => $val) {
            if (array_key_exists($key, $map) && $map[$key] > $val) {
                continue;
            }

            $map[$key] = $val;
        }

        return $map;
    }
}