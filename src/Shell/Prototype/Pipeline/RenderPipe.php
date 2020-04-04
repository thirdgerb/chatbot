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
use Commune\Message\Blueprint\IntentMsg;
use Commune\Shell\Blueprint\Session\ShlSession;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RenderPipe extends ASessionPipe
{
    protected function before($session)
    {
        return $session;
    }

    /**
     * @param ShlSession $session
     * @return ShlSession
     */
    protected function after($session)
    {
        $outputs = $session->getShellOutputs();
        $renderer = $session->renderer;

        $newOutputs = [];
        foreach ($outputs as $output) {

            $message = $output->message;
            // 普通消息
            if (!$message instanceof IntentMsg) {
                $newOutputs[] = $output;
                continue;
            }

            if ($message instanceof IntentMsg) {
                $template = $renderer->findTemplate($message->getIntentName());

                // 模板不存在, 则不渲染.
                if (empty($template)) {
                    $newOutputs[] = $output;

                // 模板存在, 使用模板渲染.
                } else {
                    $messages = $template->render($message);
                    foreach ($messages as $message) {
                        $newOutputs[] = $output->derive($message);
                    }
                }
            }
        }

        // 替换原来的回复消息.
        $session->addShellOutputs($newOutputs);
        return $session;
    }


}