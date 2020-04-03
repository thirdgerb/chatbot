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

use Commune\Framework\Prototype\Session\ASessionPipe;
use Commune\Message\Constants\OutgoingIntents;
use Commune\Message\Prototype\IIntentMsg;
use Commune\Shell\Blueprint\Session\ShlSession;

/**
 * 与 Ghost 进行同步通讯的环节
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellMessengerPipe extends ASessionPipe
{

    /**
     * @param ShlSession $session
     * @return ShlSession
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
                $session->addShellOutputs([
                    new IIntentMsg(OutgoingIntents::INTERCOM_FAILURE)
                ]);
                return $session;
            }

            // 获取推送到收件箱里的输出消息.
            $outputs = $messenger->fetchOutputs(
                $session->shell->getShellName(),
                $session->getChatId()
            );

            // 整理需要发送的消息.
            if (!empty($outputs)) {
                $session = $this->receiveDeliveringMessage($session, $outputs);
            }

        } catch (\Exception $e) {
            // todo 目前还不知道要抛出哪些异常.
        }
        return $session;
    }

    /**
     * @param ShlSession $session
     * @return ShlSession
     */
    protected function after($session)
    {
        return $session;
    }

    /**
     * @param ShlSession $session
     * @param array $outputs
     * @return ShlSession
     */
    protected function receiveDeliveringMessage(ShlSession $session, array $outputs) : ShlSession
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