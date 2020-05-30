<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Context;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Param
{
    /**
     * 字段名
     * @return string
     */
    public function getName() : string;

    /**
     * 是否是列表字段.
     * @return bool
     */
    public function isList() : bool;


    /**
     * @param mixed $value
     * @return mixed
     */
    public function parse($value);

    /**
     * 默认值
     * @return mixed
     */
    public function getDefault();
}