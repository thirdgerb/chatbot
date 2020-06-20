<?php

use Commune\Platform;

return [

    new Platform\Stdio\StdioPlatformConfig([
        'id' => 'stdio',
        'concrete' => Platform\Stdio\StdioPlatform::class,
        'adapter' => Platform\Stdio\StdioAdapter::class,
        'bootShell' => 'demo',
        'bootGhost' => true,
        'options' => [
            Platform\Stdio\StdioOption::class => [

            ],
        ],
    ]),

];
