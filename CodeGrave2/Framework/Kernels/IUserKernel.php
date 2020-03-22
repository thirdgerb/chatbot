<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Kernels;

use Commune\Framework\Blueprint\ChatApp;
use Commune\Framework\Blueprint\Conversation\ReactionMessage;
use Commune\Framework\Blueprint\LogInfo;
use Commune\Message\Blueprint\ConvoMsg;
use Commune\Shell\Blueprint\Kernel\UserKernel;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Platform\Request;
use Commune\Shell\Platform\Response;
use Commune\Shell\Platform\Server;
use Commune\Framework\Blueprint\Chat;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IUserKernel implements UserKernel
{
    /**
     * @var ChatApp
     */
    protected $app;

    /**
     * @var Shell
     */
    protected $shell;

    /**
     * @var LogInfo
     */
    protected $logInfo;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var Chat
     */
    protected $chat;

    public function onMessage(
        Request $request,
        Response $response
    ): void
    {
        // TODO: Implement onMessage() method.
    }

    public function onResponse(
        Response $response
    ): void
    {

    }

    /*--------- 双通推送 ---------*/

    public function startDuplexPush(): void
    {
        if (!$this->server->isDuplex()) {
            $this->server->console->error(
                $this->logInfo->serverIsNotDuplex()
            );
            return;
        }

        $this->server->loopDuplexSending([$this, 'duplexPush']);
    }

    public function duplexPush() :void
    {
        // 遍历所有的 chat 通道.
        foreach ($this->server->getEstablishedChats() as $chatId) {
            // 向单个 chat 通道推送任务. 这里可能要用协程, 也可能要用 sleep 等待.
            $this->server->addChatSendingTask(function() use ($chatId) {
                $this->deliverChatMessage($chatId);
            });
        }
    }

    /**
     * 尝试双工地发送某一个 chat 的消息.
     * @param string $chatId
     */
    protected function deliverChatMessage(string $chatId) : void
    {

        $shellName = $this->shell->name;
        $chat = $this->chat;

        $messages = [];
        while ($message = $this->chat->subscribe($chat, [$shellName])) {
            $messages[] = $message;
        }

        $delivery = $this->deliverCheck($chat, $messages);

        // 需要发送的消息
        $buffer = $this->renderDelivery($delivery);

        // 通道如果不存在了.
        if (!$this->server->isEstablished($chatId)) {
            $this->restoreUnSentMessages($delivery);
            return;
        }

        // 遍历地发送消息给客户端.
        foreach ($buffer as $message) {
            $this->server->send($chatId, $message);
        }
    }

    /**
     * @param Chat $chat
     * @param array $messages
     * @return array
     */
    protected function deliverCheck(Chat $chat, array $messages) : array
    {

    }

    protected function restoreUnSentMessages(array $messages) : array
    {

    }

    /**
     * @param ReactionMessage[] $delivery
     * @return ConvoMsg[]
     */
    protected function renderDelivery(array $delivery) : array
    {

    }

    public function startOfflinePush(): void
    {
        if (!$this->server->isOfflineSendAble()) {
            $this->server->console->error(
                $this->logInfo->serverCanNotSendOffline()
            );
            return;
        }


    }


}