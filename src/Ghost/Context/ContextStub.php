<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Memory\Memorable;
use Commune\Blueprint\Ghost\Memory\Stub;
use Commune\Ghost\Memory\AStub;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $contextId
 * @property-read string $contextName
 */
class ContextStub extends AStub implements Stub
{
    public function toMemorable(Cloner $cloner): ? Memorable
    {
        return $cloner->getContext($this->contextId, $this->contextName);
    }

}