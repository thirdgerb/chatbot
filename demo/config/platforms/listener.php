<?php


use Commune\Platform\Shell;
use Commune\Platform\Shell\Tcp;

return new Shell\TcpBroadcastShellPlatformConfig([
     'id' => 'listener',
     'concrete' => Tcp\SwlBroadcastShellPlatform::class,
     'bootShell' => 'listener_shell',
     'bootGhost' => false,
     'providers' => [

     ],
     'options' => [
         Tcp\SwlDuplexShellOption::class => [
             'adapterName' => Tcp\SwlBroadcastAdapter::class,
             'tableSize' => 10000,
             'serverOption' => [
                 'host' => '127.0.0.1',
                 'port' => '9504',
             ],
         ],
     ],

]);