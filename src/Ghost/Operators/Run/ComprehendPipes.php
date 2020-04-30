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
use Commune\Blueprint\Ghost\Pipe\ComprehendPipe;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ComprehendPipes extends Runner
{
    public function invoke(Cloner $cloner): ? Operator
    {
        $task = $this->process->currentTask();

        // 检查局部的管道.
        $stageDef = $task->findStageDef($cloner);
        $pipes = $stageDef->comprehendPipes($cloner);

        // 得到全局的管道, 或者局部的管道.
        $pipes = empty($pipes)
            ? $cloner->ghost->getConfig()->components
            : $pipes;

        // 构建管道.
        $pipeline = $cloner->buildPipeline($pipes, ComprehendPipe::HANDLE, function($cloner){
            return $cloner;
        });

        // 执行管道.
        $pipeline($cloner);

        return null;
    }


}