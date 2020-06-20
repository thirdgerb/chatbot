<?php

use Commune\Host\Prototype\HostProtoConfig;

return new HostProtoConfig([

    'id' => 'demo',

    'name' => 'demo',

    // ghost 的配置
    'ghost' => include __DIR__ . '/includes/ghost.php',

    // shell 的配置
    'shells' => include __DIR__ . '/includes/shells.php',

    // 平台的配置.
    'platforms' => include __DIR__ . '/includes/platforms.php',

]);
