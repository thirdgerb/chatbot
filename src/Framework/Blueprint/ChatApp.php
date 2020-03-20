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

use Commune\Shell\Blueprint\Shell;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ChatApp
{
    public function __construct(
        ChatAppConfig $config,
        Container $processContainer = null
    );

    /**
     * 获取配置
     * @return ChatAppConfig
     */
    public function getConfig() : ChatAppConfig;

    /**
     * 启动 Shell 实例
     * @param string $shellName
     * @return Shell
     */
    public function getShell(string $shellName) : Shell;

    /**
     * @return string[]
     */
    public function getShellNames() : array;


    /*------- 容器 --------*/

    /**
     * 获取进程级容器
     * @return Container
     */
    public function getProcessContainer() : Container;

    /**
     * 获取对话级容器
     * @return Container
     */
    public function getConversationContainer() : Container;

}