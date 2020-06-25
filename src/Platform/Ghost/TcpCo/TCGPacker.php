<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Ghost\TcpCo;

use Commune\Blueprint\Platform\Adapter;
use Commune\Blueprint\Platform\Packer;
use Swoole\Coroutine\Server\Connection;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TCGPacker implements Packer
{

    /**
     * @var TCGPlatform
     */
    public $platform;

    /**
     * @var Connection
     */
    public $conn;

    /**
     * @var string
     */
    public $data;

    /**
     * TCGPacker constructor.
     * @param TCGPlatform $platform
     * @param Connection $conn
     * @param string $data
     */
    public function __construct(TCGPlatform $platform, Connection $conn, string $data)
    {
        $this->platform = $platform;
        $this->conn = $conn;
        $this->data = $data;
    }


    public function isInvalid(): ? string
    {
        // TODO: Implement isInvalid() method.
    }

    public function adapt(string $adapterName): Adapter
    {
        // TODO: Implement adapt() method.
    }

    public function fail(\Throwable $e): void
    {
        $this->platform->catchExp($e);
    }

    public function destroy(): void
    {
        unset(
            $this->conn
        );
    }


}