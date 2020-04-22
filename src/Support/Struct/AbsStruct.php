<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Struct;

use Commune\Support\Arr\ArrayAbleToJson;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsStruct implements Struct, \Serializable
{
    use ArrayAbleToJson;

    const GETTER_PREFIX = '__get_';


    /**
     * @var array
     */
    protected $_data;

    public function __construct(array $data = [])
    {

        $stub = static::stub();
        $data = $this->_filter($data) + $stub;

        $error = static::validate($data);
        if (isset($error)) {
            throw new InvalidStructException("struct validate data fail: $error");
        }

        $this->_data = $this->_recursiveConstruct($data);
    }

    /**
     * 过滤
     * @param array $data
     * @return array
     */
    public function _filter(array $data) : array
    {
        return $data;
    }

    /**
     * @param array $data
     * @return static
     */
    public static function create(array $data = []): Struct
    {
        return new static($data);
    }

    public function __get($name)
    {
        if (method_exists($this, $method = static::GETTER_PREFIX . $name)) {
            return $this->{$method}();
        }
        return $this->_data[$name] ?? null;
    }

    public function __set($name, $value)
    {
        if (!static::isRelation($name)) {
            $this->_data[$name] = $value;
            return;
        }

        if (!is_array($value)) {
            throw new InvalidStructException("relation $name data must be array");
        }

        $struct = static::getRelationClass($name);

        if (!static::isListRelation($name)) {
            $this->_data[$name] = call_user_func([$struct, 'create'], $value);
            return;
        }

        foreach ($value as $key => $val) {
            $value[$key] = call_user_func([$struct, 'create'], $val);
        }

        $this->_data[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    private function _recursiveConstruct(array $data) : array
    {
        $relations = static::relations();
        if (empty($relations)) {
            return $data;
        }

        foreach ($relations as $field => $structType) {
            $isArray = $this->_isArrayFieldName($field);
            $field = $isArray ? $this->_fieldWithOutArrMark($field) : $field;

            // 不能不存在
            if (!array_key_exists($field, $data)) {
                throw new InvalidStructException("relation field $field is missing");
            }

            if (!$isArray) {
                $data[$field] = $this->_buildRelatedStruct(
                    $structType,
                    $data[$field]
                );
            } else {
                foreach ($data[$field] as $key => $value) {
                    $data[$field][$key] = $this->_buildRelatedStruct(
                        $structType,
                        $value
                    );
                }
            }
        }

        return $data;
    }

    public static function isRelation(string $fieldName): bool
    {
        $relations = static::relations();
        if (empty($relations)) {
            return false;
        }

        return array_key_exists($fieldName, $relations)
            || array_key_exists($fieldName . '[]', $relations);
    }

    public static function isListRelation(string $fieldName): bool
    {
        $relations = static::relations();
        if (empty($relations)) {
            return false;
        }
        return array_key_exists($fieldName . '[]', $relations);
    }


    public static function getRelationNames() : array
    {
        $names = [];
        foreach(static::relations() as $relation) {
            $names[] = self::_isArrayFieldName($relation)
                ? self::_fieldWithOutArrMark($relation)
                : $relation;
        }
        return $names;
    }

    public static function getRelationClass(string $fieldName): ? string
    {
        $relations = static::relations();

        return $relations[$fieldName] ?? $relations[$fieldName . '[]'] ?? null;
    }


    private static function _fieldWithOutArrMark(string $field) : string
    {
        return substr($field, 0, -2);
    }

    private static function _isArrayFieldName($field) : bool
    {
        return substr($field, -2, 2) === '[]';
    }


    private function _buildRelatedStruct(string $type, $data) : Struct
    {
        if (is_object($data) && is_a($data, $type, TRUE)) {
            return $data;
        }

        if (!is_array($data)) {
            throw new InvalidStructException("relation value for type $type must be array");
        }
        return call_user_func(
            [$type, 'create'],
            $data
        );
    }

    public function toArray(): array
    {
        $data = $this->_data;

        $relations = static::relations();
        if (empty($relations)) {
            return $data;
        }

        foreach ($relations as $field => $structType) {
            $isArray = $this->_isArrayFieldName($field);
            $field = $isArray ? $this->_fieldWithOutArrMark($field) : $field;

            if (!isset($data[$field])) {
                continue;
            }

            if ($isArray) {
                /**
                 * @var Struct $value
                 */
                foreach($data[$field] as $key => $value) {
                    $data[$field][$key] = $value->toArray();
                }

            } else {
                $data[$field] = $data[$field]->toArray();
            }
        }
        return $data;
    }


    public function __destruct()
    {
        // 防止不回收垃圾.
        $this->_data = [];
    }

    public function serialize()
    {
        return $this->toJson();
    }

    public function unserialize($serialized)
    {
        return static::create(json_decode($serialized, true));
    }


}