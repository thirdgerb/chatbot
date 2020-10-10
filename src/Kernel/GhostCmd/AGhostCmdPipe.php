<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\GhostCmd;

use Commune\Blueprint\Kernel\Protocols\AppResponse;
use Commune\Blueprint\Kernel\Protocols\GhostRequest;
use Commune\Blueprint\Kernel\Protocols\GhostResponse;
use Commune\Container\ContainerContract;
use Commune\Framework\Command\TRequestCmdPipe;
use Commune\Kernel\ClonePipes\AClonePipe;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Psr\Log\LoggerInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AGhostCmdPipe extends AClonePipe implements RequestCmdPipe
{
    use TRequestCmdPipe;

    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        // 默认权限校验.
        $policies = $this->getAuthPolicies();
        if (!empty($policies)) {
            $auth = $this->cloner->auth;
            foreach ($policies as $policy) {
                if (!$auth->allow($policy)) return $next($request);
            }
        }

        $response = $this->tryHandleCommand($request, $next);
        return $response instanceof GhostResponse
            ? $response
            : $request->response(AppResponse::HOST_LOGIC_ERROR);
    }


    public function getContainer(): ContainerContract
    {
        return $this->cloner->container;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->cloner->logger;
    }



}