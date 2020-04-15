<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Abstracted;

use Commune\Framework\Blueprint\Abstracted\Comprehension;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Babel\BabelSerializable;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IComprehension implements Comprehension
{
    use ArrayAbleToJson;

    const PROPERTIES = [
        'intent' => IIntention::class,
        'choice' => IChoice::class,
        'command' => ICmdStr::class,
        'emotion' => IEmotion::class,
        'recognition' => IRecognition::class,
        'tokenization' => ITokenization::class,
        'soundLike' => ISoundLike::class
    ];

    protected $data = [];

    public function __construct()
    {
    }

    public function __get($name)
    {
        if (!isset(self::PROPERTIES[$name])) {
            return null;
        }

        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        $className = self::PROPERTIES[$name];

        return $this->data[$name] = new $className;

    }

    public function toArray(): array
    {
        $data = [];
        foreach (self::PROPERTIES as $name => $className) {
            $data[$name] = $this->{$name}->toArray();
        }
        return $data;
    }

    public function toSerializableArray(): array
    {
        return $this->toArray();
    }

    public static function getSerializableId(): string
    {
        return static::class;
    }

    public static function fromSerializableArray(array $data): ? BabelSerializable
    {
        $object = new static();
        foreach ($data as $name => $values) {
            if (array_key_exists($name, self::PROPERTIES)) {

                $property = $object->{$name};
                foreach ($values as $key => $value) {
                    $property->{$key} = $value;
                }
            }
        }
        return $object;
    }

    public function __destruct()
    {
        // 方便垃圾回收.
        $this->data = [];
    }

}