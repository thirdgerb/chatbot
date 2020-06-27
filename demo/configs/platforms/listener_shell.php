<?php


use Commune\Platform\Shell;
use Commune\Platform\Shell\Tcp;

return new Shell\TcpBroadcastShellPlatformConfig([
     'id' => 'listener_shell',
     'concrete' => Tcp\SwlAsyncShellPlatform::class,
     'bootShell' => 'demo_shell',
     'bootGhost' => false,
     'providers' => [

     ],
     'options' => [
         Tcp\SwlAsyncShellOption::class => [
             'adapterName' => Tcp\SwlAsyncBroadcastAdapter::class,
             'tableSize' => 10000,
             'serverOption' => [
                 'host' => '127.0.0.1',
                 'port' => '9504',
             ],
         ],
     ],

]);