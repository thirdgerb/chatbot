<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\GhostCmd\Super;

use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Kernel\GhostCmd\AGhostCmd;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ScopeCmd extends AGhostCmd
{
    const SIGNATURE = 'scope';

    const DESCRIPTION = '查看用户自己的数据';

    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $data = $this->cloner->scope->toArray();
        $data['traceId'] = $this->cloner->getTraceId();

        $this->info(json_encode($data, ArrayAndJsonAble::PRETTY_JSON));
    }


}