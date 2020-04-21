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
     * 注册一个 resolver
     *
     * @param string $serializableId
     * @param callable $serializer
     * @param callable $unSerializer
     */
    public function register(
        string $serializableId,
        callable $serializer,
        callable $unSerializer
    ) : void;

    /**
     * 注册一个 BabelSerializable
     * @param string $serializable   类名
     */
    public function registerSerializableClass(string $serializable) : void;

    /**
     * 检查一个 serializableId 是否已经注册.
     * @param string $serializableId
     * @return bool
     */
    public function hasRegistered(string $serializableId) : bool;

    /**
     * 将对象变为数组
     * @param BabelSerializable $serializable
     * @return array
     */
    public function encodeToArray(BabelSerializable $serializable) : array;

    /**
     * 从数组还原
     * @param array $data
     * @return static|null
     */
    public function decodeFromArray(array $data) : ? BabelSerializable;


    /**
     * 序列化.
     * @param BabelSerializable $serializable
     * @return string
     */
    public function serialize($serializable) : string;


    /**
     * 反序列化.
     * @param string $input
     * @return null|mixed 如果为 null, 表示无法反序列化.
     */
    public function unSerialize(string $input);


}