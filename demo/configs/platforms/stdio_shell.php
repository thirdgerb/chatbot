<?php

use Commune\Platform;
use Commune\Platform\Libs;

return new Platform\Shell\StdioSinglePlatformConfig([

    'id' => 'stdio_shell',

    'bootShell' => 'demo_shell',
    'bootGhost' => true,

    'options' => [
    ],
]);