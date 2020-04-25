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
interface StructFieldReflector
{
    public function getStructType() : string;

    public function getFieldName() : string;

    public function validateValue($value) : ? string /* errorMsg */ ;

    /**
     * 默认的过滤输入值.
     * @param mixed $value
     * @return mixed
     */
    public function filterValue($value);
}