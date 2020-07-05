<?php

use Commune\Platform;
use Commune\Platform\Libs;

return new Platform\Shell\StdioConsolePlatformConfig([

    'id' => 'stdio',

    'bootShell' => 'stdio_shell',
    'bootGhost' => true,

    'options' => [
    ],
]);