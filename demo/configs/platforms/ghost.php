<?php

use Commune\Platform;
use Commune\Framework\Providers;

return new Platform\Ghost\TcpCoGhostPlatformConfig([

    'id' => 'ghost',

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
                'serverOption' => [
                ],
            ],
            /**
             * @see TcpAdapterOption
             */
            'adapterOption' => [
                'tcpAdapter' => Platform\Ghost\Tcp\SwlCoBabelGhostAdapter::class,
                'receiveTimeout' => 0
            ],

        ],
    ],
]);

