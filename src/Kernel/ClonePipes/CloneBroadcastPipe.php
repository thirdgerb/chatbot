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
use Commune\Contracts\Messenger\GhostMessenger;
use Commune\Contracts\Messenger\MessageDB;
use Commune\Kernel\Protocals\IGhostRequest;
use Commune\Kernel\Protocals\IShellOutputRequest;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Protocals\Intercom\OutputMsg;
use Commune\Protocals\IntercomMsg;


/**
 * 消息的发送, 管理.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneBroadcastPipe extends AClonePipe
{
    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        // 如果消息是从别的 Session 传来的投递消息, 直接返回给 Shell.
        // 用这种方式, 可以让不同的 SessionId 通过 AsyncInput 来传递消息给其它的 SessionId
        $app = $this->cloner->getApp();
        $isDelivery = $request->isDelivery();
        if ($isDelivery) {
            $response = $request->output(
                $app->getId(),
                $app->getName(),
                $request->getInput()->getMessage()
            );

        } else {
            /**
             * 继续走后面的多轮对话逻辑.
             * @var GhostResponse $response
             */
            $response = $next($request);
        }

        // 请求不合法, 则不需要广播.
        if (!$response->isForward()) {
            return $response;
        }

        // 处理异步消息.
        $this->sendAsyncMessages($request);

        // 获取所有的输出.
        $outputs = $this->cloner->getOutputs();

        $response->mergeOutputs($outputs);
        $outputs = $response->getOutputs();

        // 设置 convoId
        $input = $this->cloner->input;
        $this->setConvoId($input, ...$outputs);


        // 保存消息.
        $this->recordBatch(
            $request->getTraceId(),
            $request->getFromApp(),
            $request->getFromSession(),
            $input,
            ...$outputs
        );

        // 广播消息
        $this->broadcast($request, $response);

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
        if ($this->cloner->isStateless() || !$response->isForward()) {
            return;
        }

        $routes = $this->cloner->storage->shellSessionRoutes;
        $selfShellId = $request->getFromApp();

        // 如果是同步消息, 则不广播.
        unset($routes[$selfShellId]);
        if ($request->isAsync()) {
            $routes[$selfShellId] = $request->getFromSession();
        }

        if (empty($broadcasting)) {
            return;
        }

        /**
         * @var Broadcaster $broadcaster
         */
        $broadcaster = $this->cloner->container->get(Broadcaster::class);

        $traceId = $request->getTraceId();
        $batchId = $request->getBatchId();

        // 广播所有消息. 最好是协程.
        foreach ($routes as $shellId => $sessionId) {
            $broadcaster->publish(
                $shellId,

                // 使用一个空的响应, 携带 ID 来作为事件发送.
                IShellOutputRequest::instance(
                    true,
                    $sessionId,
                    $traceId,
                    $batchId,
                    []
                )
            );
        }

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

    protected function sendAsyncMessages(GhostRequest $request) : void
    {
        $inputs = $this->cloner->getAsyncInputs();
        $deliveries = $this->cloner->getAsyncDeliveries();

        if (empty($inputs) && $deliveries) {
            return;
        }

        /**
         * @var GhostMessenger $messenger
         */
        $messenger = $this->cloner->container->get(GhostMessenger::class);

        $appId = $this->cloner->getAppId();
        $traceId = $request->getTraceId();

        // 发送异步的输入消息
        if (!empty($inputs)) {
            $inputRequests = array_map(function(InputMsg $input) use ($appId, $traceId){
                return IGhostRequest::instance(
                    $appId,
                    true,
                    $input,
                    '',
                    [],
                    null,
                    false,
                    $traceId
                );
            }, $inputs);
            $messenger->asyncSendRequest(...$inputRequests);
        }

        // 发送异步的投递消息.
        if (!empty($deliveries)) {
            $deliveryRequests = array_map(function(InputMsg $input) use ($appId, $traceId) {
                return IGhostRequest::instance(
                    $appId,
                    true,
                    $input,
                    '',
                    [],
                    null,
                    true,
                    $traceId
                );

            }, $deliveries);
            $messenger->asyncSendGhostRequest(...$deliveryRequests);
        }
    }

    /**
     * @param string $traceId
     * @param string $fromApp
     * @param string $fromSession
     * @param IntercomMsg $message
     * @param IntercomMsg ...$messages
     */
    protected function recordBatch(
        string $traceId,
        string $fromApp,
        string $fromSession,
        IntercomMsg $message,
        IntercomMsg ...$messages
    ) : void
    {
        /**
         * @var MessageDB $messageDB
         */
        $messageDB = $this->cloner->container->get(MessageDB::class);
        $messageDB->recordMessages(
            $traceId,
            $fromApp,
            $fromSession,
            $message,
            ...$messages
        );

    }
}