<?php

require_once __DIR__ .'/../vendor/autoload.php';

$config = include  __DIR__ . '/configs/config.php';
$config['reactorProviders'] = [
    \Commune\Chatbot\App\Platform\SwooleConsole\SwooleConsoleServiceProvider::class,
];


$app = new \Commune\Chatbot\Framework\ChatApp($config);
$app->getServer()->run();

