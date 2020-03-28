<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Blueprint;

use Commune\Container\ContainerContract;
use Commune\Ghost\Blueprint\Ghost;
use Commune\Shell\Blueprint\Shell;


/**
 * 机器人实例.
 * 包含 Shell 或者 Ghost, 特殊情况下也可以启动两个.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface Chatbot
{

    public function bootstrap() : void;

    public function getChatbotName() : string;

    /**
     * 进程级容器.
     * @return ContainerContract
     */
    public function getProcContainer() : ContainerContract;

    /**
     * 获取 Ghost 实例
     *
     * @return Ghost
     */
    public function getGhost() : Ghost;

    /**
     * 获取 Shell 实例
     *
     * @param string $shellName
     * @return Shell
     */
    public function getShell(string $shellName) : Shell;

}