<?php


use Commune\Platform\Shell;

return new Shell\TcpDuplexShellPlatformConfig([
    'id' => 'duplex',
    'name' => 'DuplexTcpPlatform',
    'desc' => '双工的 TCP Platform',
    'concrete' => Shell\Tcp\SwlDuplexShellPlatform::class,
    'bootShell' => 'demo_shell',
    'bootGhost' => false,
    'providers' => [

    ],
    'options' => [
        Shell\Tcp\SwlDuplexShellOption::class => [
            'adapterName' => Shell\Tcp\SwlBroadcastAdapter::class,
            'tableSize' => 10000,
            'serverOption' => [
                'host' => '127.0.0.1',
                'port' => '9503',

            ],
        ],
    ],

]);