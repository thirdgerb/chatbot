<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Run;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Routes\Activate\Retain;
use Commune\Ghost\Operators\Stage\Activation;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Challenge extends Runner
{
    public function invoke(Cloner $cloner): ? Operator
    {
        $blocking = $this->process->popBlocking();

        if (!isset($blocking)) {
            return null;
        }

        $loser = $this->process->challenge($blocking);
        if (isset($loser)) {
            $this->process->blockTask($loser);
            return new Activation($blocking, Retain::class);
        }

        // 输了自己回去
        $this->process->blockTask($blocking);
        return null;
    }


}