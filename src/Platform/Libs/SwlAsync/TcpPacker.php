<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Libs\SwlAsync;

use Commune\Blueprint\Platform\Adapter;
use Commune\Blueprint\Platform\Packer;
use Swoole\Server;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TcpPacker implements Packer
{

    /**
     * @var SwlAsyncPlatform
     */
    public $platform;

    /**
     * @var int
     */
    public $fd;

    /**
     * @var string
     */
    public $data;

    /**
     * @var Server
     */
    public $server;

    public $from;

    /**
     * SwlAsyncPacker constructor.
     * @param SwlAsyncPlatform $platform
     * @param Server $server
     * @param int $fd
     * @param $from
     * @param string $data
     */
    public function __construct(
        SwlAsyncPlatform $platform,
        Server $server,
        int $fd,
        int $from,
        string $data
    )
    {
        $this->platform = $platform;
        $this->fd = $fd;
        $this->data = $data;
        $this->server = $server;
        $this->from = $from;
    }


    public function isInvalid(): ? string
    {
        return null;
    }

    public function getData() : string
    {
        return $this->data;
    }

    public function adapt(string $adapterName, string $appId): Adapter
    {
        return new $adapterName($this, $appId);
    }

    public function fail(string $error): void
    {
        $sessionId = $this->platform->getFdSession($this->fd);

        if (!empty($sessionId)) {
            $this->platform->unsetSessionRoute($sessionId);
        }

        if ($this->exists()) {
            $this->server->send($this->fd, $error);
            $this->server->close($this->fd);
        }
    }

    public function exists() : bool
    {
        return $this->server->exists($this->fd);
    }

    public function destroy(): void
    {
        unset(
            $this->platform,
            $this->server
        );
    }


}