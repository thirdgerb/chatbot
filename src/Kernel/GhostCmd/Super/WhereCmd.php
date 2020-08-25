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
class WhereCmd extends AGhostCmd
{
    const SIGNATURE = 'where 
        {--s|stage : 查看 stage 的数据}
        {--c|context : 查看 context 的数据}
    ';

    const DESCRIPTION = '查看当前对话场景的数据';

    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $current = $this->cloner->runtime->getCurrentProcess()->getAwait();

        $i = 0;
        if ($message['--stage']) {
            $arr = $current->findStageDef($this->cloner)->toMeta()->toArray();
            $this->info("stage: ");
            $this->info(JsonMsg::fromArr($arr));
            $i ++;
        }

        if ($message['--context']) {
            $arr = $current->findContextDef($this->cloner)->toMeta()->toArray();
            $this->info("context: ");
            $this->info(JsonMsg::fromArr($arr));
            $i ++;
        }

        if ($i === 0) {
            $this->error("require argument --context or --stage!");
        }

    }


}