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
     * 序列化成字符串.
     * @return string
     */
    public function babelSerialize() : string;

    /**
     * 对于 Babel 而言的 ID
     * @return string
     */
    public static function getSerializableId() : string;

    /**
     * 反序列化.
     * @param string $input
     * @return static|null
     */
    public static function babelUnSerialize(string $input) : ? BabelSerializable;
}