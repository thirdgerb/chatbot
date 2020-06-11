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

use ArrayAccess;
use Commune\Support\Utils\ArrayUtils;
use Commune\Support\Utils\TypeUtils;
use InvalidArgumentException;
use Commune\Support\Arr\ArrayAbleToJson;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsStruct implements Struct, ArrayAccess, \Serializable
{
    use ArrayAbleToJson;


    const STRICT = true;

    /**
     * @var array
     */
    protected $_data = [];

    public function __construct(array $data = [])
    {
        $stub = static::stub();
        $data = $data + $stub;

        $this->_shouldBeArray($data);
        $this->_constructData($data);
    }

    private function _shouldBeArray($data) : void
    {
        // 构建关系
        if (!is_array($data)) {
            throw new InvalidStructException(
                static::class
                . ' struct data should be array'
            );
        }
    }

    protected function _constructData(array $data) : void
    {
        $this->fill($data);
        $this->_shouldBeArray($this->_data);
        $error = static::validate($this->_data);

        if (!empty($error)) {
            throw new InvalidStructException(static::class . ' invalid struct data , ' . $error);
        }
    }

    /**
     * 过滤数据. 自定义规则
     * @param array $data
     */
    public function fill(array $data) : void
    {
        foreach ($data as $key => $val) {
            $this->__set($key, $val);
        }
    }

    /**
     * 默认的校验机制是用注解加反射
     * @param array $data
     * @return null|string
     */
    public static function validate(array $data): ? string /* errorMsg */
    {
        return StructReflections::validate(static::class, $data);
    }

    /**
     * @param array $data
     * @return static
     */
    public static function create(array $data = []): Struct
    {
        return new static($data);
    }

    /*------- magic --------*/

    public function __get($name)
    {
        if (method_exists($this, $method = static::GETTER_PREFIX . $name)) {
            return $this->{$method}($name);
        }

        return $this->_data[$name] ?? null;
    }

    public function __set($name, $value)
    {
        // 用自定义 method 来赋值.
        if (method_exists($this, $method = static::SETTER_PREFIX . $name)) {
            $this->{$method}($name, $value);
            return;
        }

        // 关系类型转换.
        if (static::isRelation($name)) {
            $this->_data[$name] = $this->_parseRelation($name, $value);
            return;
        }

        // 默认的赋值.
        $reflector = StructReflections::getFieldReflector(static::class, $name);
        // 允许赋值
        if (!isset($reflector)) {
            $this->_data[$name] = $value;
            return;
        }

        // 弱类型, 则允许转换
        if (!static::STRICT) {
            $value = $reflector->filterValue($value);
        }

        // 校验.
        $error = $reflector->validateValue($value);
        if (isset($error)) {
            throw new InvalidStructException(static::class . " set field [$name] fail : $error");
        }

        $this->_data[$name] = $value;
        return;
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __unset($name)
    {
        unset($this->_data[$name]);
    }

    /*------- iterator --------*/

    public function getIterator()
    {
        return new \ArrayIterator($this->_data);
    }

    /*------- array able --------*/

    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }




    /*------- private --------*/

    /**
     * @param string $field
     * @param $value
     * @return Struct[]|Struct|null
     */
    private function _parseRelation(string $field, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        $structType = static::getRelationClass($field);
        $isList = static::isListRelation($field);

        if (!$isList) {

            return $this->_buildRelatedStruct(
                $field,
                $structType,
                $value
            );

        } elseif ($isList && is_array($value)) {
            $result = [];
            foreach ($value as $key => $val) {
                // null
                if (is_null($val)) {
                    continue;
                }

                // build
                $result[$key] = $this->_buildRelatedStruct(
                    $field . '[' . $key . ']',
                    $structType,
                    $val
                );
            }

            return $result;
        }

        throw new InvalidArgumentException("invalid relation value for $field");
    }

    /*------- relation --------*/

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


    /**
     * 获取关系的名称.
     * @return array
     */
    public static function getRelationNames() : array
    {
        $names = [];
        $relations = array_keys(static::relations());
        foreach($relations as $relation) {
            $names[] = TypeUtils::isListTypeHint($relation)
                ? TypeUtils::pureListTypeHint($relation)
                : $relation;
        }
        return $names;
    }

    /**
     * 获取某个 relation 关联的类
     * @param string $fieldName
     * @return null|string
     */
    public static function getRelationClass(string $fieldName): ? string
    {
        $relations = static::relations();

        return $relations[$fieldName] ?? $relations[$fieldName . '[]'] ?? null;
    }


    private function _buildRelatedStruct(string $field, string $type, $data) : Struct
    {
        // 如果是封装好的对象.
        if (is_object($data) && is_a($data, $type, TRUE)) {
            return $data;
        }

        if (!is_array($data)) {
            throw new InvalidArgumentException(static::class . " relation field $field only accept Struct object or array, ". TypeUtils::getType($data) . ' given');
        }

        return call_user_func(
            [$type, 'create'],
            $data
        );
    }

    /*------- array --------*/

    /**
     * 递归生成数组数据.
     * @return array
     */
    public function toArray(): array
    {
        $data = $this->_data;
        return ArrayUtils::recursiveToArray($data);
    }

    /*------- doc --------*/

    public static function getDocComment(): string
    {
        $r = new \ReflectionClass(static::class);
        return $r->getDocComment();
    }

    /*------- serialize --------*/

    public function __destruct()
    {
        // 防止不回收垃圾.
        $this->_data = [];
    }

    public function serialize()
    {
        return json_encode($this->toArray());
    }

    public function unserialize($serialized)
    {
        $this->_constructData(json_decode($serialized, true));
    }



}