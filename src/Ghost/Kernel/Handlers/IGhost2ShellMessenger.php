<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Kernel\Handlers;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Contracts\Messenger\Messenger;
use Commune\Blueprint\Kernel\Handlers\Ghost2ShellMessenger;
use Commune\Blueprint\Kernel\Protocals\CloneRequest;
use Commune\Blueprint\Kernel\Protocals\CloneResponse;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Contracts\Messenger\Broadcast;
use Commune\Ghost\Support\ValidateUtils;
use Commune\Support\Utils\TypeUtils;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IGhost2ShellMessenger implements Ghost2ShellMessenger
{

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @param CloneResponse $protocal
     * @return GhostResponse
     */
    public function __invoke($protocal)
    {
        ValidateUtils::isArgInstanceOf($protocal, CloneResponse::class, true);

        // 发送异步消息.
        $asyncInputs = $protocal->getAsyncInputs();
        if (!empty($asyncInputs)) {
            /**
             * @var Messenger $messenger
             */
            $messenger = $this->cloner->container->get(Messenger::class);
            $messenger->asyncSendInput2Ghost(...$asyncInputs);
        }

        // 如果请求不成功, 或者请求做了无状态处理, 都直接返回结果.
        if (!$protocal->isSuccess() || $this->cloner->isStateless()) {
            return $this->directResponse($protocal);
        }

        // 会先广播, 再返回响应.
        return $this->broadcastResponse($protocal);

    }

    protected function directResponse(CloneResponse $cloneResponse) : GhostResponse
    {
        $ghostRequest = $cloneResponse->getRequest()->getRequest();
        $tiny = $ghostRequest->requireTinyResponse();

        $input = $cloneResponse->getInput();
        $shellName = $input->getShellName();
        $shellSessionId = $this->cloner->storage->getShellSessionId($shellName);



    }

}