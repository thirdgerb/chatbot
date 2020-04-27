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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Memory\Memorable;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $className
 */
class MemStub extends AStub
{

    public function toMemorable(Cloner $cloner): ? Memorable
    {
        $className = $this->className;
        return call_user_func([$className, 'find'], $cloner);
    }


}