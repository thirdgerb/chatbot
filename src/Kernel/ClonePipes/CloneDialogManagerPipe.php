<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\ClonePipes;

use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Ghost\IOperate\OStart;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneDialogManagerPipe extends AClonePipe
{
    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        $operator = new OStart($this->cloner);

        // 可以考虑用中间件来定义自己 Start operator, 根据情况决定.
        $this->cloner->runDialogManager($operator);
        unset($next);
        return $request->response();
    }


}