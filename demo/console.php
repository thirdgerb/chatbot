<?php

require_once __DIR__ .'/../vendor/autoload.php';

// 将第一个参数作为 scene
$scene = $argv[1] ?? '';
$_GET['scene'] = $scene;

$config = include  __DIR__ . '/configs/config.php';
$config['processProviders'] = [
    \Commune\Chatbot\App\Platform\ReactorStdio\RSServerProvider::class,
];

$app = new \Commune\Chatbot\Framework\ChatApp($config);
$app->getServer()->run();

