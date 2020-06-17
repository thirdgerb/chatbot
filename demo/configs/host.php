<?php


/**
 * @see \Commune\Blueprint\Configs\HostConfig
 */
return [

    'id' => 'demo',
    'name' => 'demo',
    'providers' => [],
    'options' => [],
    'ghost' => include __DIR__ . '/ghost/demo.php',
    'shells' => [
        include __DIR__ .'/shells/demo.php',
    ],
    'platforms' => [
        include __DIR__ . '/platforms/stdio.php',
    ],
];
