<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Redirection;

use Commune\Chatbot\Ghost\Blueprint\Ghost;
use Commune\Chatbot\Ghost\Blueprint\Redirector;
use Commune\Chatbot\Ghost\Supports\SuggestionsMatcher;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Heard extends AbsRedirector
{
    use SuggestionsMatcher;

    public function invoke(Ghost $host): ? Redirector
    {
        $thread = $this->process()->aliveThread();

        // 开始进行各种逻辑的匹配, 判断由谁负责响应.
        return // 检验是否匹配了 Thread 定义的选项.
            $this->matchChoices()
            // 检验是否匹配了允许的命令行
            ?? $this->matchCommands()
            // 检验是否匹配了备选的意图
            ?? $this->matchHearingIntents()
            // 检验是否应该运行匹配到的意图
            ?? $this->shouldRunMatchedIntent()
            // 没有命中任何分枝, 由 Stage 负责善后.
            ?? $this->fallbackToStage();
    }

}