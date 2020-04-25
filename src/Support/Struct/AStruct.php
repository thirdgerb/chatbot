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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AStruct extends AbsStruct
{
    // 强类型校验.
    const STRICT = true;

    /**
     * 默认值
     * @return array
     */
    abstract public static function stub(): array;

    /**
     * 定义关联关系
     * @return array
     *  [
     *      string $fieldName => string $relationClassName,
     *      string "$fieldName[]" => string $relationClassName,  //数组式的.
     * ]
     */
    abstract public static function relations(): array;



}