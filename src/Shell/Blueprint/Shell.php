<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint;

use Commune\Framework\Blueprint\ChatApp;
use Commune\Framework\Blueprint\ChatAppConfig;
use Commune\Framework\Blueprint\Container;
use Commune\Framework\Blueprint\Conversation\Conversation;
use Commune\Platform\Request;
use Commune\Platform\Server;
use Commune\Shell\Blueprint\Kernel\ApiKernel;
use Commune\Shell\Blueprint\Kernel\CallbackKernel;
use Commune\Shell\Blueprint\Kernel\UserKernel;
use Psr\Container\ContainerInterface;

/**
 * 机器人的某一个 Shell.
 * 是全平台通用的.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Shell extends ContainerInterface
{
    /**
     * Shell 的名称
     * @return string
     */
    public function name() : string;

    /**
     * 进程级容器
     * @return Container
     */
    public function getProcessContainer() : Container;

    /**
     * 会话级容器
     * @return Container
     */
    public function getConversationContainer() : Container;

    /**
     * 获得一个会话级的 Conversation 容器
     *
     * @param Request $request
     * @return Conversation
     */
    public function newConversation(Request $request) : Conversation;

    /**
     * 绑定一个对象到容器, 全局共享
     * @param string $abstract
     * @param $singleton
     */
    public function bindInstance(string $abstract, $singleton) : void;

    /**
     * @return ChatAppConfig
     */
    public function getAppConfig() : ChatAppConfig;

    /**
     * 获取配置
     * @return ShellConfig
     */
    public function getShellConfig() : ShellConfig;

    /*------- 启动 --------*/

    /**
     * 初始化 Shell
     * @param Server $server
     */
    public function boot(Server $server) : void;

    /**
     * @return Server
     */
    public function getServer() : Server;

    /*------- kernel --------*/

    /**
     * @return ApiKernel
     */
    public function getApiKernel() : ApiKernel;

    /**
     * @return UserKernel
     */
    public function getUserKernel() : UserKernel;

    /**
     * @return CallbackKernel
     */
    public function getCallbackKernel() : CallbackKernel;

}