<?php


use Commune\Platform\Shell;

return new Shell\TcpDuplexShellPlatformConfig([
    'id' => 'duplex',
    'concrete' => Shell\Tcp\SwlDuplexShellPlatform::class,
    'bootShell' => 'duplex_shell',
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