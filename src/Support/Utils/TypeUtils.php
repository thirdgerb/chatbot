<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Utils;


/**
 * 类型检查和转换工具.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TypeUtils
{

    /**
     * 获取目标的类型
     * @param $value
     * @return string
     */
    public static function getType($value) : string
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }

}