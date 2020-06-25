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

use Swoole\Coroutine\Server\Connection;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
trait CoTcpServeTrait
{

    abstract public function getAdapterOption() : CoTcpAdapterOption;

    public function receive(Connection $conn) : void
    {
        $adapterOption = $this->getAdapterOption();

        $adapterName = $adapterOption->tcpAdapter;
        $timeout = $adapterOption->sendTimeout;

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
            $packer = new CoTcpPacker(
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