<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint;

use Commune\Framework\Contracts\Cache;
use Commune\Framework\Blueprint\Chat;
use Commune\Shell\Blueprint\Shell;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $chatbotName                   机器人的名称
 * @property-read ChatAppConfig $config                 机器人的配置
 * @property-read LogInfo $logInfo                      日志信息的方法类
 * @property-read Container $processContainer           进程级的容器
 * @property-read Container $conversationContainer      对话级的容器
 *
 */
interface ChatApp
{

    /**
     * 启动 Shell 实例
     * @param string $shellName
     * @return Shell
     */
    public function createShell(string $shellName) : Shell;

    /**
     * @return string[]
     */
    public function getShellNames() : array;

    public function getCache() : Cache;

    public function getChat() : Chat;

}