<?php

use Commune\Platform;
use Commune\Platform\Shell\Tcp;
use Commune\Platform\Libs;
use Commune\Framework\Providers;

return new Platform\Shell\TcpSyncShellPlatformConfig([

    'id' => 'sync_shell',

    'bootShell' => 'demo_shell',
    'bootGhost' => false,

    'providers' => [
        Providers\ShlMessengerBySwlCoTcpProvider::class => [
            'ghostHost' => '127.0.0.1',
            'ghostPort' => '9501',
        ],

    ],
    'options' => [
        Tcp\SwlCoShellOption::class => [
            'poolOption' => [
                'workerNum' => 2,
                'host' => '127.0.0.1',
                'port' => '9502',
                'ssl' => false,
                'serverOption' => [
                ],
            ],
            /**
             * @see TcpAdapterOption
             */
            'adapterOption' => [
                'tcpAdapter' => Tcp\SwlCoTextShellAdapter::class,
                'receiveTimeout' => 0
            ],

        ],
    ],
]);