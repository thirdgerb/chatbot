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

use Commune\Blueprint\Framework\Auth\Supervise;
use Commune\Ghost\Cmd\AGhostCmdPipe;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;

/**
 * 管理员命令的管道.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneSuperCmdPipe extends AGhostCmdPipe
{
    public function getCommandMark(): string
    {
        return '/';
    }

    public function getAuthPolicies(): array
    {
        return [Supervise::class];
    }


    public function getCommands(): array
    {
        return $this->cloner->config->superCommands;
    }

    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        if ($this->cloner->auth->allow(Supervise::class)) {
            return parent::doHandle($request, $next);
        }

        return $next($request);
    }


}