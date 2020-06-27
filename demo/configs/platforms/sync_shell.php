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
    ],
    'options' => [
        Tcp\SwlCoShellOption::class => [
            'poolOption' => [
                'workerNum' => 2,
                'host' => '127.0.0.1',
                'port' => '9502',
                'ssl' => false,
                'serverSettings' => [
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