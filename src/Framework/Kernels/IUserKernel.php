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
        foreach ($this->server->getEstablishedChats() as $chatId) {

            $this->server->addChatSendingTask(function() use ($chatId) {
                $this->deliverChatMessage($chatId);
            });


        }
    }

    protected function deliverChatMessage(string $chatId) : void
    {

        $shellName = $this->shell->name;
        $chat = $this->chat;

        $messages = [];
        while ($message = $this->chat->subscribe($chat, [$shellName])) {
            $messages[] = $message;
        }

        $delivery = $this->deliverCheck($chat, $messages);

        $buffer = $this->renderDelivery($delivery);
        $response = $this->server->makeResponse($chatId);

        // 通道如果不存在了.
        if (!$this->server->isEstablished($chatId)) {
            $this->restoreUnSentMessages($delivery);
            return;
        }

        $response->buffer($buffer);
        $response->sendResponse();
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