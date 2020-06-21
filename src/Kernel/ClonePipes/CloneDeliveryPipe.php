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
use Commune\Contracts\Messenger\Broadcaster;
use Commune\Contracts\Messenger\MessageDB;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Protocals\Intercom\OutputMsg;
use Commune\Contracts\Messenger\Messenger;


/**
 * 消息的发送, 管理.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneDeliveryPipe extends AClonePipe
{
    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        /**
         * @var GhostResponse $response
         */
        $response = $next($request);

        // 请求不合法, 则不要响应.
        if (!$response->isSuccess()) {
            return $response;
        }

        // 处理异步消息.
        $asyncInputs = $this->cloner->getAsyncInputs();
        $this->sendAsyncInputs($asyncInputs);

        // 设置 convoId
        $input = $this->cloner->input;
        $outputs = $this->cloner->getOutputs();
        $this->setConvoId($input, ...$outputs);

        // 保存消息.
        $this->recordBatch($input, $outputs);

        // 广播消息
        $this->broadcast($request, $response);

        // 判断是否携带消息体. 异步请求不带消息, 或者 shell 主动要求不带消息.
        $noneOutputs = $request->isAsync() || $request->requireTinyResponse();

        // 携带消息体.
        if (!$noneOutputs) {
            $response->setOutputs($outputs);
        }

        // 返回响应给客户端处理.
        return $response;
    }

    /**
     * 尝试广播消息.
     * 广播有很多种条件:
     *
     * @param GhostRequest $request
     * @param GhostResponse $response
     */
    protected function broadcast(GhostRequest $request, GhostResponse $response) : void
    {
        // 无状态则不需要广播.
        if ($this->cloner->isStateless() || !$response->isSuccess()) {
            return;
        }

        $routes = $this->cloner->storage->shellSessionRoutes;

        $includeSelf = $request->isAsync();
        $selfShellName = $request->getShellName();

        // 广播给所有渠道.
        $broadcasting = [];
        foreach ($routes as $shellName => $shellId) {
            if ( $includeSelf || $shellName !== $selfShellName) {
                $broadcasting[$shellName] = $response->divide($shellName, $shellId);
            }
        }

        if (empty($broadcasting)) {
            return;
        }

        /**
         * @var Broadcaster $broadcaster
         */
        $broadcaster = $this->cloner->container->get(Broadcaster::class);
        $broadcaster->publish(...$broadcasting);
    }


    /**
     * 同步消息设置正确的 convoId
     * @param InputMsg $input
     * @param OutputMsg ...$outputs
     */
    protected function setConvoId(InputMsg $input, OutputMsg ...$outputs) : void
    {
        $convoId = $this->cloner->getConversationId();
        $input->setConvoId($convoId);
        foreach ($outputs as $output) {
            $output->setConvoId($convoId);
        }
    }

    /**
     * @param InputMsg[] $inputs
     */
    protected function sendAsyncInputs(array $inputs) : void
    {
        if (empty($inputs)) {
            return;
        }

        /**
         * @var Messenger $messenger
         */
        $messenger = $this->cloner->container->get(Messenger::class);
        foreach ($inputs as $input) {
            $messenger->asyncSendGhostInputs($input);
        }
    }

    /**
     * @param InputMsg $input
     * @param OutputMsg[] $outputs
     */
    protected function recordBatch(InputMsg $input, array $outputs) : void
    {
        /**
         * @var MessageDB $messageDB
         */
        $messageDB = $this->cloner->container->get(MessageDB::class);
        $messageDB->recordMessages($input, ...$outputs);
    }
}