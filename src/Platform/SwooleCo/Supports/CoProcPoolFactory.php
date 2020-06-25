<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\SwooleCo\Supports;

use Swoole\Coroutine;
use Swoole\Process;

/**
 * 协程进程池.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CoProcPoolFactory
{

    /**
     * @var Process\Pool
     */
    protected $pool;


    /**
     * @var CoProcPoolOption
     */
    protected $option;

    /**
     * CoProcPoolFactory constructor.
     * @param CoProcPoolOption $option
     */
    public function __construct(CoProcPoolOption $option)
    {
        $this->option = $option;
        $this->pool = $this->initPool($option);
    }

    /**
     * 关闭 platform.
     */
    public function shutdown(): void
    {
        $this->pool->shutdown();
    }

    public function getPool() : Process\Pool
    {
        return $this->pool;
    }


    /**
     * 创建一个 Server
     * @return Coroutine\Server
     * @throws \Swoole\Exception
     */
    public function createServer() : Coroutine\Server
    {
        $option = $this->option;

        // 设置 server 启动参数.
        $server = new Coroutine\Server(
            $option->host,
            $option->port,
            $option->ssl,
            true
        );

        //收到15信号关闭服务
        Process::signal(SIGTERM, function () use ($server) {
            $server->shutdown();
        });

        return $server;

    }


    /*-------- 获取协程进程池 --------*/

    /**
     * 初始化进程池.
     *
     * @param CoProcPoolOption $option
     * @return Process\Pool
     */
    protected function initPool(CoProcPoolOption $option) : Process\Pool
    {
        $pool = new Process\Pool($option->workerNum);

        // 设置 Server 的配置.
        $serverOption = $option->serverOption;

        // 必须允许协程.
        $serverOption['enable_coroutine'] = true;
        $pool->set($serverOption);

        return $pool;
    }
}