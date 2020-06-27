<?php


use Commune\Platform\Shell;

return new Shell\TcpDuplexShellPlatformConfig([
    'id' => 'duplex_shell',
    'concrete' => Shell\Tcp\SwlAsyncShellPlatform::class,
    'bootShell' => 'demo_shell',
    'bootGhost' => false,
    'providers' => [

    ],
    'options' => [
        Shell\Tcp\SwlAsyncShellOption::class => [
            'adapterName' => \Commune\Platform\Libs\SwlAsync\SwlAsyncBroadcastAdapter::class,
            'tableSize' => 10000,
            'serverOption' => [
                'host' => '127.0.0.1',
                'port' => '9503',

            ],
        ],
    ],

]);