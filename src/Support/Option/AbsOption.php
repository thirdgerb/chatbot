<?php

namespace Commune\Support\Option;

use Commune\Support\Utils\StringUtils;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Schematic\Entry;
use InvalidArgumentException;

/**
 * 基于Entry 实现的配置.
 * 关键就在于要定义 stub.
 * 这样可以提供默认值, 可以mock
 * 注解也有数据可以对应.
 * 比普通的Entry要麻烦许多, 但自带默认值.
 *
 */
abstract class AbsOption extends Entry implements Option
{
    use ArrayAbleToJson;

    /**
     * 定义 option 的ID 字段是哪一个
     * determine getId() method use which key as id
     */
    const IDENTITY = '';

    /**
     * Structure constructor.
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct(array $data = [])
    {
        // 注意不要让错误的stub 污染配置.
        // stub array will fill missed keys of data
        $data = $this->init($data);

        // you can validate input array
        $error = $this->validate($data);
        if (!empty($error)){
            throw new InvalidArgumentException(
                static::class
                . ' construct fail: ' . $error
                . ', ' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        }

        parent::__construct($data);
    }

    public static function getIdentityName() : string
    {
        return static::IDENTITY;
    }

    protected function init(array $data) : array
    {
        return $data + static::stub();
    }

    /**
     * 当option 作为列表元素被使用的时候
     * getId() 能让我们知道哪个是哪个
     * 所以最好每个可作为列表元素的, 都重写本方法.
     *
     * when there are list of options,
     * getId() could identify them
     *
     * @return string|mixed
     */
    public function getId() :  string
    {
        $stub = static::stub();
        if (isset($stub[static::IDENTITY])) {
            $key = static::IDENTITY;
            return (string) $this->{$key};
        }
        return static::class;
    }


    /**
     * option 数据的哈希, 可以用来比较异同.
     * @return string
     */
    public function getHash() : string
    {
        return sha1(static::class . '::' . $this->toJson());
    }

    /**
     * 当前 Option 对象的自我简介.
     * 适合用于列表场景. 用 id => brief 的形式列出多个 option
     * 也适合用于全文搜索.
     *
     * @return string
     */
    public function getBrief() : string
    {
        return (string) $this->getId();
    }


    /**
     * 使用ID + stub, 创建一个 option对象.
     *
     * generate a option object by ID and stub data
     *
     * @param $id
     * @param array $data
     * @return static
     */
    public static function createById($id, array $data = []) : Option
    {
        if (!empty(static::IDENTITY)) {
            $data[static::IDENTITY] = $id;
        }

        return new static($data);
    }

    /**
     * 合并一个数组到当前option, 生成一个新的option对象.
     *
     * merge $data to current option
     * @param array $data
     * @return static
     */
    public function merge(array $data)
    {
        return new static($data + $this->toArray());
    }

    /**
     * 默认的校验方法. 如果有错误, 将错误提示字符串返回.
     * 会作为 抛出异常的 message
     *
     * if validate success, return null
     * otherwise return error message as string
     *
     * @param array $data
     * @return null|string
     */
    public static function validate(array $data) : ? string
    {
        if (!empty(static::IDENTITY) && !isset($data[static::IDENTITY])) {
            return 'identity field is empty';
        }
        return null;
    }

    /**
     * 转成数组. 由于 Schematic/Entry 类用了private 方法, 所以只好复制了该库
     * 强烈建议写公共库时少用private
     * 看起来符合设计理念的地方, 往往实际用起来是需要改的.
     * 从这个角度来看, python 的做法反而好.
     * 面向对象的封装, 对程序员本该就是约定而已.
     *
     * get data as array
     *
     * Entry class make property 'data' as private property
     * thus can not get it directly
     *
     * I think maybe php programmer use 'private' too much
     * Maybe not necessary for script language
     *
     * @return array
     */
    public function toArray() : array
    {
        return $this->toRecursiveArray();
    }

    /**
     * 获取当前 option 的原始数组.
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * 递归地获取对象的值.
     * @return array
     */
    public function toRecursiveArray() : array
    {
        $keys = array_keys(static::stub());
        $data = [];
        foreach($keys as $key) {
            $value = $this->{$key};

            if (static::isListAssociation($key)) {
                foreach ($value as $index => $item) {
                    if ($item instanceof Option) {
                        $data[$key][$index] = $item->toArray();
                    } else {
                        $data[$key][$index] = $item;
                    }
                }

            } elseif ($value instanceof Option) {
                $data[$key] = $value->toArray();
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }


    /**
     * 保证 option 可以作为数组foreach
     *
     * make Option class traversable
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }

    /**
     * 获取 sub option 的定义.
     * @return array
     */
    public static function getAssociations() : array
    {
        return static::$associations;
    }

    /**
     * 检查一个key 是不是另一个option 对象
     * @param string $key
     * @return bool
     */
    public static function isAssociation(string $key) : bool
    {
        return isset(static::$associations[$key])
            || static::isListAssociation($key);
    }

    /**
     * 检查一个key 是不是一个option的数组
     * @param string $key
     * @return bool
     */
    public static function isListAssociation(string $key) : bool
    {
        $index = $key.'[]';
        return isset(static::$associations[$index]);
    }

    /**
     * 获取已有的 sub option class
     *
     * @param string $key
     * @return null|string
     */
    public static function getAssociationClass(string $key) : ? string
    {
        return static::$associations[$key]
            ?? static::$associations[$key . '[]' ]
            ?? null;
    }

    /**
     * 根据注解, 获取 Option 的参数定义.
     * @return array [ [name, type, desc], [] ]
     * @throws
     */
    public static function getProperties() : array
    {
        $r = new \ReflectionClass(static::class);
        $properties = StringUtils::fetchPropertyAnnotationsDetails(
            $r->getDocComment(),
            '@property-read'
        );
        $results = [];
        foreach ($properties as $item) {
            $results[$item[0]] = $item;
        }
        return $results;
    }

    /**
     * 根据 @description 注解, 获取option 的说明.
     *
     * fetch description of the option from @description annotation
     * @return string
     * @throws
     */
    public static function getDescription() : string
    {
        $r = new \ReflectionClass(static::class);
        return StringUtils::fetchDescAnnotation($r->getDocComment());
    }

}