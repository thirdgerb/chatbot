<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Predefined;

use Commune\Message\Blueprint\Message;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Babel\TBabelSerializable;
use Commune\Support\DI\TInjectable;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AMessage implements Message
{
    use ArrayAbleToJson, TBabelSerializable, TInjectable;

    /**
     * 毫秒级的时间戳.
     * @var int
     */
    protected $createdAt;

    /**
     * AMessage constructor.
     * @param float $createdAt
     */
    public function __construct(float $createdAt = null)
    {
        $this->createdAt = $createdAt ?? microtime(true);
    }

    public function toArray(): array
    {
        return [
            'type' => static::getSerializableId(),
            'data' => $this->toSerializableArray()[0],
        ];
    }

    public function getCreatedAt() : int
    {
        return $this->createdAt
            ?? $this->createdAt = time();
    }

    final public function getInterfaces(): array
    {
        return static::getInterfacesOf(Message::class);
    }

}