<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Libs\SwlCo;

use Commune\Platform\AbsPlatform;
use Swoole\Coroutine\Server\Connection;

/**
 * 使用 Swoole Coroutine TCP Server 的平台端.
 * 获取一个 Connection, 接受客户端信息, 并同步发送响应.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @mixin AbsPlatform
 */
trait TcpPlatformServeTrait
{

    /**
     * @return TcpPlatformOption
     */
    abstract public function getAdapterOption() : TcpPlatformOption;

    /**
     * 接受到一个协程的 TCP 连接.
     * @param Connection $conn
     */
    public function receive(Connection $conn) : void
    {
        $adapterOption = $this->getAdapterOption();

        $adapterName = $adapterOption->tcpAdapter;
        $timeout = $adapterOption->receiveTimeout;

        // 循环处理数据.
        while (true) {
            //接收数据
            $data = $conn->recv($timeout);

            // 退出.
            if (empty($data)) {
                $conn->close();
                break;
            }

            // 通用的 Tcp Packer
            $packer = new TcpPacker(
                $this,
                $conn,
                $data
            );

            $success = $this->onPacker($packer, $adapterName);
            if (!$success) {
                break;
            }
        }
    }



}