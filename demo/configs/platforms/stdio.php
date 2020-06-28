<?php

use Commune\Platform;
use Commune\Platform\Libs;

return new Platform\Shell\StdioSinglePlatformConfig([

    'id' => 'stdio',

    'bootShell' => 'stdio_shell',
    'bootGhost' => true,

    'options' => [
    ],
]);