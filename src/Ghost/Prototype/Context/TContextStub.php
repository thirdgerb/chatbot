<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Context;

use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Ghost\Exceptions\ConvoInstanceException;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\TArrayData;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
trait TContextStub
{
    use ArrayAbleToJson, TArrayData;


    public function merge(array $data): void
    {
        throw new ConvoInstanceException(__METHOD__);
    }

    public function reset(array $data = null): void
    {
        throw new ConvoInstanceException(__METHOD__);
    }

    public function getId(): string
    {
        throw new ConvoInstanceException(__METHOD__);
    }

    public function toNewNode(): Node
    {
        throw new ConvoInstanceException(__METHOD__);
    }


    public function isInstanced(): bool
    {
        return false;
    }

    public function getInterfaces(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return $this->data;
    }



}