<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Struct\Reflection;

use Commune\Support\Struct\Struct;

/**
 * 结构体的属性定义.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface StructProperty
{
    /**
     * 所属 Struct
     * @return string
     */
    public function getStructName() : string;

    /**
     * 名字
     * @return string
     */
    public function getName() : string;

    /*-------- 描述 ------*/

    /**
     * 属性的简介.
     * @return string
     */
    public function getDesc() : string;

    /**
     * 属性输入的提示.
     * @return string
     */
    public function getQuery() : string;


    /*-------- 定义 ------*/

    /**
     * @return bool
     */
    public function isList() : bool;

    /**
     * @return bool
     */
    public function allowNulls() : bool;

    /**
     * @return mixed|null
     */
    public function getDefaultValue();


    /*-------- 关联关系 ------*/

    /**
     * @return bool
     */
    public function isRelation() : bool;

    /**
     * @return null|string
     */
    public function getRelationClass()  : ? string;

    /*-------- 类型定义 ------*/

    /**
     * @param $value
     * @return null|string
     */
    public function validateValue($value) : ? string /* errorMsg */ ;

    /**
     * 默认的过滤输入值.
     *
     * @param $value
     * @param bool $strict
     * @return mixed
     */
    public function parseValue($value, bool $strict);

    /**
     * 类型定义输出为字符串.
     * @return string
     */
    public function getTypeHint() : string;

    /**
     * 校验的规则名称.
     * @return string[]
     */
    public function getValidateRules() : array;

    /*-------- getter setter ------*/

    /**
     * !注意避免出现死循环.
     *
     * @param Struct $struct
     * @param $value
     */
    public function set(Struct $struct, $value) : void;

    /**
     * @param Struct $struct
     * @return mixed
     */
    public function get(Struct $struct);
}