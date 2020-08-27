<?php

use Commune\Platform;
use Commune\Platform\Libs;

return new Platform\Shell\StdioShellPlatformConfig([
    'id' => 'console',

    'bootShell' => 'demo_shell',
]);
