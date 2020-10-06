<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Providers;

use Commune\Container\ContainerContract;
use Commune\Contracts\Messenger\ShellMessenger;
use Commune\Contracts\ServiceProvider;
use Commune\Framework\Messenger\ShlMessengerBySwlCoTcp;
use Commune\Support\Swoole\ClientOption;
use Commune\Support\Utils\TypeUtils;
use Psr\Log\LoggerInterface;
use Swoole\ConnectionPool;


/**
 * 基于 Swoole 的 TCP 协程 (EOF 协议) 实现的 Shell 2 Ghost 通信.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $ghostHost
 * @property-read string $ghostPort
 * @property-read float $connectTimeout
 * @property-read float $receiveTimeout
 * @property-read int $poolSize
 *
 */
class ShlMessengerBySwlCoTcpProvider extends ServiceProvider
{
    public function getDefaultScope(): string
    {
        return self::SCOPE_PROC;
    }

    public static function stub(): array
    {
        return [
            'ghostHost' => '',
            'ghostPort' => '',
            'connectTimeout' => 0.3,
            'receiveTimeout' => 0.3,
            'poolSize' => ConnectionPool::DEFAULT_SIZE
        ];
    }

    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(
            ShellMessenger::class,
            function(ContainerContract $app) {

                $logger = $app->make(LoggerInterface::class);
                $option = new ClientOption([
                    'host' => $this->ghostHost,
                    'port' => $this->ghostPort,
                    'connectTimeout' => $this->connectTimeout,
                    'receiveTimeout' => $this->receiveTimeout,
                    'poolSize' => $this->poolSize,

                ]);

                return new ShlMessengerBySwlCoTcp(
                    $logger,
                    $option
                );
            }
        );
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        return TypeUtils::requireFields($data, ['ghostHost', 'ghostPort'])
            ??  parent::validate($data);
    }

}