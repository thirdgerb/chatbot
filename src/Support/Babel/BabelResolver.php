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
 * 将字符串反序列化为对象的机制. 用于各端之间传输信息.
 * 至于用到 json 还是 proto 还是什么, 是否加密, 这里不管.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface BabelResolver
{

    /**
     * 序列化.
     * @param mixed $serializable
     * @return string
     */
    public function serialize($serializable) : string;


    /**
     * 反序列化.
     * @param string $input
     * @return null|mixed 如果为 null, 表示无法反序列化. 可以返回原来的字符串.
     */
    public function unserialize(string $input);

    public function encodeToArray(BabelSerializable $serializable): array;

    public function decodeFromArray(array $data): ? BabelSerializable;


}