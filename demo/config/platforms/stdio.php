<?php

use Commune\Platform;
use Commune\Platform\Libs;

return new Platform\Shell\StdioShellPlatformConfig([
    'id' => 'stdio',
    'name' => 'Stdio',
    'desc' => '基于 Stdio 实现的本地 Platform',
    'bootShell' => 'demo_shell',
]);
