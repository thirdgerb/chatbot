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

use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class JsonResolver implements BabelResolver
{
    protected $transformers = [];

    public function registerSerializableClass(string $serializable) : void
    {
        if (!is_a(
            $serializable,
            BabelSerializable::class,
            TRUE
        )) {
            throw new \InvalidArgumentException("$serializable is not instance of " . BabelSerializable::class);
        }

        /**
         * @var BabelSerializable $serializable
         */

        static::register(
            call_user_func([$serializable, 'getTransferId']),
            function(BabelSerializable $obj) {
                return $obj->toTransferArr();
            },
            [$serializable, 'fromTransferArr']
        );
    }

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
    ) : void
    {
        $this->transformers[$serializableId] = [$serializer, $unSerializer];
    }

    public function hasRegistered(
        string $serializableId
    ) : bool
    {
        return array_key_exists($serializableId, $this->transformers);
    }

    public function encodeToArray(BabelSerializable $serializable): array
    {
        return [
            'bbl_type' => $serializable->getTransferId(),
            'data' => $serializable->toTransferArr(),
        ];
    }

    public function decodeFromArray(array $arr): ? BabelSerializable
    {
        $type = $arr['bbl_type'] ?? null;
        $data = $arr['data'] ?? null;

        if (empty($type) || !is_array($data)) {
            return null;
        }

        if ($this->hasRegistered($type)) {
            $unSerializer = $this->transformers[$type][1];
            return call_user_func($unSerializer, $data);
        }


        $serializableId = StringUtils::dotToNamespaceSlash($type);

        if (is_a($serializableId, BabelSerializable::class, TRUE)
        ) {
            $this->registerSerializableClass($serializableId);
            return static::decodeFromArray($arr);
        }

        return null;
    }


    /**
     * 序列化, 通常有一个加密环节.
     * @param BabelSerializable $serializable
     * @return string
     */
    public function serialize($serializable) : string
    {
        if ($serializable instanceof BabelSerializable) {
            $id = $serializable->getTransferId();

            if (!static::hasRegistered($id)) {
                static::registerSerializableClass(get_class($serializable));
            }

            $data = $this->encodeToArray($serializable);
            return json_encode(
                $data,
                JSON_UNESCAPED_UNICODE
            );
        }

        return serialize($serializable);
    }

    /**
     * 反序列化. 通常还有一个解密环节.
     * @param string $input
     * @return null|mixed 如果为 null, 表示无法反序列化.
     */
    public function unserialize(string $input)
    {
        $data = json_decode($input, true);
        if (!is_array($data)) {
            $un = unserialize($input);
            return $un === false ? null : $un;
        }

        if (
            count($data) === 2
            && isset($data['bbl_type'])
            && is_string($data['bbl_type'])
            && isset($data['data'])
            && is_array($data['data'])
        ) {
            return $this->decodeFromArray($data);
        }

        return $data;
    }


}