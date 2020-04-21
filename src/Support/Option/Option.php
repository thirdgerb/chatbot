<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Option;

use IteratorAggregate;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 将数组转化为结构体. 
 * 
 * 接受数组作为数据, 并通过强类型来调用数组中的数据.
 * 可以根据约定, 将子属性转化为同样的对象.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Option extends ArrayAndJsonAble, IteratorAggregate
{

    /**
     * 默认样板数据. 为真实数据提供默认值.
     *
     * default data of option
     * @return array
     */
    public static function stub() : array;

    /**
     * ID 对应的字段名.
     * @return string
     */
    public static function getIdentityName() : string;


    /**
     * 使用ID + stub, 创建一个 option对象.
     *
     * @param $id
     * @param array $data
     * @return static
     */
    public static function createById($id, array $data = []) : Option;

    /**
     * 默认的校验方法. 如果有错误, 将错误提示字符串返回.
     * 会作为 抛出异常的 message
     *
     * if validate success, return null
     * otherwise return error message as string
     *
     * @param array $data
     * @return null|string   error message
     */
    public static function validate(array $data) : ? string;

    /*------- 实例属性 -------*/


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
    public function getId() : string;

    /**
     * 当前 Option 对象的自我简介.
     * 适合用于列表场景. 用 id => brief 的形式列出多个 option
     * 也适合用于全文搜索.
     *
     * @return string
     */
    public function getBrief() : string;


    /*------- methods -------*/

    /**
     * 合并一个数组到当前option, 生成一个新的option对象.
     *
     * @param array $data
     * @return static
     */
    public function merge(array $data);


    /*------- 转型 -------*/

    /**
     * option 数据的哈希, 可以用来比较异同.
     * @return string
     */
    public function getHash() : string;

    /**
     * 获取当前 option 的原始数组.
     * @return array
     */
    public function getData(): array;

    /**
     * 递归地获取对象的值.
     * @return array
     */
    public function toRecursiveArray() : array;


    /*------- reflections -------*/


    /**
     * 获取 sub option 的定义.
     * @return string[]
     */
    public static function getAssociations() : array;

    /**
     * 检查一个key 是不是另一个option 对象
     * @param string $key
     * @return bool
     */
    public static function isAssociation(string $key) : bool;

    /**
     * 检查一个key 是不是一个option的数组
     * @param string $key
     * @return bool
     */
    public static function isListAssociation(string $key) : bool;

    /**
     * 获取已有的 sub option class
     *
     * @param string $key
     * @return null|string
     */
    public static function getAssociationClass(string $key) : ? string;

    /**
     * 根据 @description 注解, 获取option 的说明.
     *
     * @return string
     */
    public static function getDescription() : string;

}