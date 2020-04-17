<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Pipeline;

use Commune\Framework\Exceptions\ChatRequestException;
use Commune\Framework\Prototype\Session\ASessionPipe;
use Commune\Message\Constants\SystemIntents;
use Commune\Message\Predefined\IIntentMsg;
use Commune\Message\Predefined\SystemInts\IntercomFailureInt;
use Commune\Shell\Blueprint\Session\ShellSession;

/**
 * 与 Ghost 进行同步通讯的环节
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellMessengerPipe extends ASessionPipe
{

    /**
     * @param ShellSession $session
     * @return ShellSession
     */
    protected function before($session)
    {
        try {
            $messenger = $session->messenger;

            // 发送同步消息.
            $success = $messenger->sendInput($session->ghostInput);

            // 同步信号
            // 如果发送不成功, 要告知用户失败.
            if (!$success) {
                throw new ChatRequestException('send sync input fail');
            }

        } catch (\Exception $e) {

            $session->addShellOutputs([
                new IntercomFailureInt($e->getMessage(), $e->getCode())
            ]);
            $this->stopPropagation();
        }
        return $session;
    }

    /**
     * @param ShellSession $session
     * @return ShellSession
     */
    protected function after($session)
    {
        $messenger = $session->messenger;

        // 获取推送到收件箱里的输出消息.
        $outputs = $messenger->fetchOutputs(
            $session->shell->getShellName(),
            $session->getChatId()
        );

        // 整理需要发送的消息.
        if (!empty($outputs)) {
            $session = $this->receiveDeliveringMessage($session, $outputs);
        }

        return $session;
    }

    /**
     * @param ShellSession $session
     * @param array $outputs
     * @return ShellSession
     */
    protected function receiveDeliveringMessage(ShellSession $session, array $outputs) : ShellSession
    {
        $now = time();
        $beforeDelivery = [];
        $delivery = [];

        foreach ($outputs as $output) {
            if ($now < $output->deliverAt) {
                $beforeDelivery[] = $output;
            } else {
                $delivery[] = $output;
            }
        }

        $session->addShellOutputs($delivery);
        // 不需要立刻发送的, 重新入队.
        $session->messenger->sendOutputs($beforeDelivery);

        return $session;
    }

}