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
use Commune\Message\Host\Convo\Verbal\JsonMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StageCmd extends AGhostCmd
{
    const SIGNATURE = 'stage';

    const DESCRIPTION = '查看当前对话场景的数据';

    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $current = $this->cloner->runtime->getCurrentProcess()->getAwait();
        $arr = $current->findStageDef($this->cloner)->toMeta()->toArray();

        $this->info(JsonMsg::fromArr($arr));
    }


}