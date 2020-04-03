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
trait TBabelSerializable
{
    abstract public function __sleep() : array;

    public function toSerializableArray(): array
    {
        $data = [];
        $recursive = [];
        foreach ($this->__sleep() as $field) {
            $value = $this->{$field};
            if ($value instanceof BabelSerializable) {
                $value = Babel::getResolver()->toSerializingArray($value);
                $recursive[] = $field;
            } elseif (is_object($value)) {
                $value = serialize($value);
                $recursive[] = $field;
            }
            $data[$field] = $value;
        }
        return [$data, $recursive];
    }


    public static function getSerializableId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }

    /**
     * @param array $input
     * @return static|null
     */
    public static function fromSerializableArray(array $input): ? BabelSerializable
    {
        list($info, $recursive) = $input;

        $object = new static();

        foreach ($recursive as $field) {
            $value = $info[$field] ?? null;

            if (is_array($value)) {
                $value = isset($value)
                    ? Babel::getResolver()->fromSerializableArray($value)
                    : null;

            } elseif (is_string($value)) {
                $value = unserialize($value);
            }

            $object->{$field} = $value;
            unset($info[$field]);
        }

        foreach ($info as $field => $value) {
            $object->{$field} = $value;
        }

        return $object;
    }
}