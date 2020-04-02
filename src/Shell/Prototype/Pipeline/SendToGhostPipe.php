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

use Commune\Shell\Blueprint\Session\ShlSession;


/**
 * 与 Ghost 进行同步通讯的环节
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SendToGhostPipe extends AShellPipe
{
    /**
     * @param ShlSession $session
     * @param callable $next   Messenger 通常是最后一个管道.
     * @return ShlSession
     */
    public function doHandle(ShlSession $session, callable $next): ShlSession
    {

        try {

            // 发送同步消息.
            $success = $session
                ->messenger
                ->sendInput($session->ghostInput);

            // 获取推送到收件箱里的输出消息.
            $outputs = $session
                ->messenger
                ->fetchOutputs(
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

        return $next($session);
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

        $session->setShellOutputs($delivery);
        // 不需要立刻发送的, 重新入队.
        $session->messenger->sendOutputs($beforeDelivery);

        return $session;
    }

}