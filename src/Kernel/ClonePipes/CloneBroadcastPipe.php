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
        // 如果消息是从别的 Session 传来的投递消息, 直接广播给 Shell.
        // 用这种方式, 可以让不同的 SessionId 通过 AsyncInput 来传递消息给其它的 SessionId
        $isDelivery = $request->isDelivery();
        if ($isDelivery) {
            $response = $request->response();

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


        // 获取所有的输出.
        $outputs = $this->cloner->getOutputs();

        $response->mergeOutputs($outputs);
        $outputs = $response->getOutputs();

        // 设置 convoId
        $input = $request->getInput();
        $this->setConvoId($input, ...$outputs);

        $container = $this->cloner->container;

        // 保存消息.
        if ($container->has(MessageDB::class)) {
            $this->recordBatch(
                $request->getTraceId(),
                $request->getFromApp(),
                $request->getFromSession(),
                $input,
                ...$outputs
            );
        }

        // 处理异步消息.
        if ($container->has(GhostMessenger::class)) {
            $this->sendAsyncMessages($request);
        }

        // 广播消息
        if ($container->has(Broadcaster::class)) {
            $this->broadcast($request, $response);
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
        if ($this->cloner->isStateless() || !$response->isForward()) {
            return;
        }

        $routes = $this->cloner->storage->shellSessionRoutes;

        /**
         * @var Broadcaster $broadcaster
         */
        $broadcaster = $this->cloner->container->get(Broadcaster::class);

        $broadcaster->publish(
            $request,
            $response,
            $routes
        );
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
        // Async Input 是机器人发给机器人的请求消息
        $inputs = $this->cloner->getAsyncInputs();
        // deliveries 是机器人要另一个机器人回复给 session 所有用户的消息.
        $deliveries = $this->cloner->getAsyncDeliveries();

        if (empty($inputs) && empty($deliveries)) {
            return;
        }

        /**
         * 获得 Messenger
         * @var GhostMessenger $messenger
         */
        $messenger = $this->cloner->container->get(GhostMessenger::class);

        // 发送异步的输入消息
        $this->sendAsyncGhostMessages(
            $request,
            $messenger,
            false,
            $inputs
        );

        // 发送异步的投递消息.
        $this->sendAsyncGhostMessages(
            $request,
            $messenger,
            true,
            $deliveries
        );
    }

    protected function sendAsyncGhostMessages(
        GhostRequest $request,
        GhostMessenger $messenger,
        bool $isDelivery,
        array $messages
    ) : void
    {
        // 发送异步的投递消息.
        if (empty($messages)) {
            return;
        }
        $traceId = $request->getTraceId();
        $fromApp = $request->getFromApp();
        $fromSession = $request->getFromSession();
        $env = $request->getEnv();

        $deliveryRequests = array_map(
            function(InputMsg $input)
                use ($traceId, $fromApp, $fromSession, $env, $isDelivery) {
                return IGhostRequest::instance(
                    $fromApp,
                    true,
                    $input,
                    '', //不需要 entry, 反正是直接投递.
                    $env,
                    null,
                    $isDelivery,
                    $traceId,
                    $fromSession
                );

            },
            $messages
        );
        $messenger->asyncSendRequest(...$deliveryRequests);
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