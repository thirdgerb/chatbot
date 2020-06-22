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

use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Struct\Reflection\StructReflection;

/**
 * 结合 PHP 数组与强类型设计的通用结构体.
 * 同时和多轮对话逻辑可以结合起来.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Struct extends ArrayAndJsonAble, \IteratorAggregate
{
    const GETTER_PREFIX = '__get_';
    const SETTER_PREFIX = '__set_';

    const FUNC_CREATE = 'create';
    const FUNC_STUB = 'stub';
    const FUNC_VALIDATE = 'validate';
    const FUNC_GET_RELATIONS = 'getRelationNames';
    const FUNC_GET_RELATION_CLASS = 'getRelationClass';
    const FUNC_IS_LIST_RELATION = 'isListRelation';

    /*------- construct -------*/

    /**
     * @param array $data
     * @return static
     */
    public static function create(array $data = []) : Struct;

    /**
     * 校验一个数组是否是合法的协议数组. 返回字符串来标记第一条错误信息
     * 可以在这里使用一些 Validator 例如:
     *  - Wixel/GUMP
     *  -
     * @param array $data
     * @return null|string
     */
    public static function validate(array $data) : ? string /* errorMsg */;

    /*------- definition -------*/

    /**
     * Message 的默认值.
     * @return array
     */
    public static function stub() : array;

    /**
     * @return array
     *[
     *   //'fieldName' => StructA::class,
     *   //'fieldName[]' => StructB::class
     * ]
     */
    public static function relations() : array;

    /*------- reflection -------*/

    /**
     * @return string[]
     */
    public static function getRelationNames() : array;

    /**
     * @param string $fieldName
     * @return bool
     */
    public static function isRelation(string $fieldName) : bool;

    /**
     * @param string $fieldName
     * @return bool
     */
    public static function isListRelation(string $fieldName) : bool;

    /**
     * @param string $fieldName
     * @return null|string
     */
    public static function getRelationClass(string $fieldName) : ? string;

    /**
     * 获取当前 Struct 预定义的反射.
     * @return StructReflection
     */
    public static function getReflection() : StructReflection;

}