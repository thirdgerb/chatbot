<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework\Render;

use Commune\Blueprint\Configs\Render\RenderOption;
use Commune\Protocals\Host\ConvoMsg;
use Commune\Protocals\Host\IntentMsg;

/**
 * HostMsg 消息的渲染器.
 * 可以把 GhostOutput 的单个 HostMsg 渲染成若干个 HostMsg, 或者选择不渲染.

 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Renderer
{
    public function matchTemplate(IntentMsg $message, array $renderOptions) : ? Template;

    /**
     * @param IntentMsg $message
     * @param RenderOption[] $renderOptions
     * @return ConvoMsg[]
     */
    public function render(IntentMsg $message, array $renderOptions) : array;
}