<?php

use Commune\Platform;
use Commune\Platform\Shell\Tcp;
use Commune\Platform\Libs;

return new Platform\Shell\TcpSyncShellPlatformConfig([
    'id' => 'sync',
    'name' => 'SyncTcpShell',
    'desc' => '同步的 tcp Shell 端, 只能接受同步消息',

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
             * @see TcpPlatformOption
             */
            'adapterOption' => [
                'tcpAdapter' => Tcp\SwlCoTextShellAdapter::class,
                'receiveTimeout' => 0
            ],

        ],
    ],
]);