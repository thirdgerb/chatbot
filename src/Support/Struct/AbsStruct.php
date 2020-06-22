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
use Commune\Support\Struct\Reflection\StructReflection;
use Commune\Support\Utils\ArrayUtils;
use Commune\Support\Utils\TypeUtils;
use Commune\Support\Arr\ArrayAbleToJson;

/**
 * 结构体的基类.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsStruct implements Struct, ArrayAccess, \Serializable
{
    use ArrayAbleToJson;

    /*------ 配置 ------*/

    /**
     * 是否是严格的类型校验.
     * 严格的情况下, 不允许过滤
     */
    const STRICT = true;

    /**
     * 可以被访问的数据.
     *
     * @var array
     */
    protected $_data = [];


    /*------ 内部参数 ------*/

    /**
     * 所有反射的缓存.
     * @var StructReflection[]
     */
    private static $reflections = [];


    public function __construct(array $data = [])
    {
        $stub = static::stub();
        $data = $data + $stub;
        $this->fill($data);
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

        //校验数据.
        $error = static::validate($this->_data);

        if (!empty($error)) {
            throw new InvalidStructException(static::class . ' invalid struct data , ' . $error);
        }
    }

    /**
     * 默认的校验机制是用注解加反射
     * @param array $data
     * @return null|string
     */
    public static function validate(array $data): ? string /* errorMsg */
    {
        return static::getStructReflection()->validate($data);
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

        $reflection = static::getStructReflection();

        if (!$reflection->isPropertyDefined($name)) {
            $this->_data[$name] = $value;
            return;
        }

        $property = $reflection->getProperty($name);

        $strict = $reflection->isStrict();

        // 类型转换.
        $value = $property->parseValue($value, $strict);

        // 强类型下赋值也要做参数校验.
        if ($strict) {
            $error = $property->validateValue($value);
        }

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

    final public static function getStructReflection(): StructReflection
    {
        $name = static::class;
        if (isset(self::$reflections[$name])) {
            return self::$reflections[$name];
        }

        return self::$reflections[$name] = static::makeReflection();
    }

    abstract protected static function makeReflection() : StructReflection;

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
        $this->fill(json_decode($serialized, true));
    }



}