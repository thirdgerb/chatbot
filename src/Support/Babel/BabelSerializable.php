<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Babel;


/**
 * 可以通过 Babel 进行传输的对象.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface BabelSerializable
{

    /**
     * 转化为数组
     * @return array
     */
    public function toSerializableArray() : array;

    /**
     * 对于 Babel 而言的 ID
     * @return string
     */
    public static function getSerializableId() : string;

    /**
     * 从数组还原。
     * @param array $data
     * @return static|null
     */
    public static function fromSerializableArray(array $data) : ? BabelSerializable;
}