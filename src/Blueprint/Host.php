<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint;

use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Exceptions\Boot\AppNotDefinedException;
use Commune\Blueprint\Configs\HostConfig;
use Commune\Blueprint\Framework\App;


/**
 * 机器人应用
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Host extends App
{
    /**
     * 机器人配置
     * @return HostConfig
     */
    public function getConfig() : HostConfig;

    /**
     * 根据 Platform 的配置, 初始化所有的服务. 
     * 
     * @param string $platformId
     * @param callable|null $onBootFailure
     * @return PlatformConfig|null
     */
    public function init(string $platformId, callable $onBootFailure = null): ? PlatformConfig;
    
    /**
     * 使用 Platform 名称正式启动一个 Platform.
     * 根据 Platform 的配置, 同时启动配置中的 Shell 和 Ghost, 如果有定义的话.
     *
     * @param string $platformId
     * @param callable|null $onBootFailure        如果启动异常, 如何处理.
     * @throws AppNotDefinedException
     */
    public function run(string $platformId, callable $onBootFailure = null) : void;

}