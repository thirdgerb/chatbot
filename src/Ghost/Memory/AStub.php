<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Memory;

use Commune\Blueprint\Ghost\Memory\Stub;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Babel\BabelSerializable;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AStub implements Stub
{
    use ArrayAbleToJson;

    /**
     * @var array
     */
    protected $data;

    /**
     * AStub constructor.
     * @param array $data
     */
    final public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }


    public function toArray(): array
    {
        return [
            'stub' => static::class,
            'data' => $this->data
        ];
    }


    public function toTransferArr(): array
    {
        return $this->data;
    }

    public static function fromTransferArr(array $data): ? BabelSerializable
    {
        return new static($data);
    }

    public static function getTransferId(): string
    {
        return static::class;
    }

    public function __destruct()
    {
        $this->data = [];
    }

}