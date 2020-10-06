<?php

use Commune\Platform;
use Commune\Platform\Libs;

return new Platform\Shell\StdioConsolePlatformConfig([

    'id' => 'console',
    'name' => 'ConsolePlatform',
    'desc' => '基于 stdio 实现的本地 platform',

    'bootShell' => 'demo_shell',
    'bootGhost' => true,

    'options' => [
    ],
]);