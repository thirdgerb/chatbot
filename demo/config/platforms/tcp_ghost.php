<?php

use Commune\Platform;
use Commune\Framework\Providers;

return new Platform\Ghost\TcpCoGhostPlatformConfig([

    'id' => 'tcp_ghost',
    'name' => 'SwooleTcpShell',
    'desc' => '使用swoole 协程 tcp 实现的 ghost',

    'bootShell' => null,
    'bootGhost' => true,

    'providers' => [
        Providers\GhtMessengerBySwlChanProvider::class => [
            'chanCapacity' => 1000,
            'chanTimeout' => 0.1,
        ],
    ],
    'options' => [
        Platform\Ghost\Tcp\SwlCoGhostOption::class => [
            'poolOption' => [
                'workerNum' => 2,
                'host' => '127.0.0.1',
                'port' => '9501',
                'ssl' => false,
                'serverSettings' => [
                ],
            ],
            /**
             * @see TcpPlatformOption
             */
            'adapterOption' => [
                'tcpAdapter' => Platform\Ghost\Tcp\SwlCoBabelGhostAdapter::class,
                'receiveTimeout' => 0
            ],

        ],
    ],
]);

