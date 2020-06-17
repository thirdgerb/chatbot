<?php

use Commune\Platform\StdioDemo\StdioDemoPlatform;

return [
    'id' => 'stdio',
    'concrete' => StdioDemoPlatform::class,
    'bootShell' => 'demo',
    'bootGhost' => true,
];
