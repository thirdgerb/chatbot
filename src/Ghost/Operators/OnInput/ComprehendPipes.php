<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\OnInput;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Pipe\ComprehendPipe;
use Commune\Ghost\Operators\FlowOperator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ComprehendPipes extends FlowOperator
{
    public function doInvoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();
        $task = $process->currentTask();

        // 检查局部的管道.
        $stageDef = $task->findStageDef($cloner);
        $pipes = $stageDef->comprehendPipes($cloner);

        // 得到全局的管道, 或者局部的管道.
        $pipes = isset($pipes)
            ? $pipes
            : $cloner->ghost->getConfig()->comprehensionPipes;

        // stageDef pipes 也可以用 [] 来表示不需要走任何中间件.
        if (empty($pipes)) {
            return null;
        }

        // 构建管道.
        $pipeline = $cloner->buildPipeline($pipes, ComprehendPipe::HANDLE, function($cloner){
            return $cloner;
        });

        // 执行管道.
        $pipeline($cloner);

        return null;
    }


}