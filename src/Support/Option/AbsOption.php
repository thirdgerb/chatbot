<?php

namespace Commune\Support\Option;

use Commune\Support\DI\TInjectable;
use Commune\Support\Struct\AStruct;

/**
 * 基于Entry 实现的配置.
 * 关键就在于要定义 stub.
 * 这样可以提供默认值, 可以mock
 * 注解也有数据可以对应.
 * 比普通的Entry要麻烦许多, 但自带默认值.
 *
 */
abstract class AbsOption extends AStruct implements Option
{
    use TInjectable;

    // Option 默认不做强类型校验, 允许弱类型转换.
    const STRICT = false;

    /**
     * 定义 option 的ID 字段是哪一个
     * determine getId() method use which key as id
     */
    const IDENTITY = '';

    public static function getIdentityName() : string
    {
        return static::IDENTITY;
    }


    /**
     * 当option 作为列表元素被使用的时候
     * getId() 能让我们知道哪个是哪个
     * 所以最好每个可作为列表元素的, 都重写本方法.
     *
     * @return string
     */
    public function getId() :  string
    {
        $key = static::getIdentityName();
        if (empty($key)) {
            return static::class;
        }
        return (string) $this->{$key};
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

        return static::create($data);
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
        return static::create($data + $this->toArray());
    }

    public function getInterfaces(): array
    {
        return static::getInterfacesOf(Option::class);
    }

    public function getTitle(): string
    {
        return '';
    }

    public function getDescription(): string
    {
        return '';
    }


}