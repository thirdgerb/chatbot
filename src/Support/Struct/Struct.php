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


/**
 * PHP 结构体. 可以用注解 @"property" 或者 @"property-read" 的方式来定义强类型.
 * 实际上通过数组的方式来生成对象. 实际持有的也是一个数组.
 *
 * 自动生成关联关系.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Struct extends ArrayAndJsonAble
{

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

    /**
     * @param array $data
     * @return static
     */
    public static function create(array $data) : Struct;

    /**
     * 校验一个数组是否是合法的协议数组. 返回字符串来标记第一条错误信息
     * 可以在这里使用一些 Validator 例如:
     *  - Wixel/GUMP
     *  -
     * @param array $data
     * @return null|string
     */
    public static function validate(array $data) : ? string /* errorMsg */;


}