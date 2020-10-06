<?php


use Commune\Platform\Shell;
use Commune\Platform\Shell\Tcp;

return new Shell\TcpBroadcastShellPlatformConfig([
     'id' => 'listener',
     'name' => '监听类 shell',
     'desc' => '监听广播事件的 shell',
     'concrete' => Tcp\SwlBroadcastShellPlatform::class,
     'bootShell' => 'listener_shell',
     'bootGhost' => false,
     'providers' => [
     ],
     'options' => [
     ],

]);