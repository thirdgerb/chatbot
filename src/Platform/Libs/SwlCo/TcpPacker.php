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

use Commune\Blueprint\Platform;
use Commune\Blueprint\Platform\Adapter;
use Commune\Blueprint\Platform\Packer;
use Swoole\Coroutine\Server\Connection;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TcpPacker implements Packer
{
    /**
     * @var Platform
     */
    public $platform;

    /**
     * @var Connection
     */
    public $conn;

    /**
     * Tcp 输入的信息.
     * @var string
     */
    protected $input;

    /**
     * CoTcpPacker constructor.
     * @param Platform $platform
     * @param Connection $conn
     * @param string $input
     */
    public function __construct(
        Platform $platform,
        Connection $conn,
        string $input
    )
    {
        $this->platform = $platform;
        $this->conn = $conn;
        $this->input = $input;
    }


    public function input() : string
    {
        return $this->input;
    }

    public function output(string $message) : void
    {
        $this->conn->send($message);
    }

    public function isInvalid(): ? string
    {
        return empty($this->input) ? "input should not be empty" : null;
    }

    public function adapt(string $adapterName, string $appId): Adapter
    {
        return new $adapterName($this, $appId);
    }

    public function fail(string $error): void
    {
        $this->conn->close();
    }

    public function destroy(): void
    {
        unset(
            $this->conn,
            $this->platform
        );
    }

}