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
use Commune\Contracts\Messenger\GhostMessenger;
use Commune\Contracts\ServiceProvider;
use Commune\Framework\Messenger\GhtMessengerBySwlChan;
use Psr\Log\LoggerInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read int $chanCapacity
 * @property-read float $pushTimeout
 * @property-read float $popTimeout
 *
 */
class GhtMessengerBySwlChanProvider extends ServiceProvider
{
    /**
     * 进程级服务
     * @return string
     */
    public function getDefaultScope(): string
    {
        return self::SCOPE_PROC;
    }

    public static function stub(): array
    {
        return [
            'chanCapacity' => 1000,
            'pushTimeout' => -1,
            'popTimeout' => -1,
        ];
    }

    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(
            GhostMessenger::class,
            function(ContainerContract $app) {
                $logger = $app->make(LoggerInterface::class);

                return new GhtMessengerBySwlChan(
                    $logger,
                    $this->chanCapacity,
                    $this->pushTimeout,
                    $this->popTimeout
                );

            }
        );
    }


}