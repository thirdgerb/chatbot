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
use Commune\Blueprint\Ghost\Memory\Recollection;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id
 */
class RecStub extends AStub
{
    /**
     * @param Cloner $cloner
     * @return Recollection|null
     */
    public function toMemorable(Cloner $cloner): ? Memorable
    {
        return $cloner->runtime->findRecollection($this->id);
    }
}