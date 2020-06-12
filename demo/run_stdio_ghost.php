<?php

use Commune\Blueprint\CommuneEnv;
use Commune\Message;
use Commune\Ghost\IGhost;
use Clue\React\Stdio\Stdio;
use React\EventLoop\Factory;
use Commune\Host\Ghost\Stdio\SGConfig;
use Commune\Host\Ghost\Stdio\SGRequest;
use Commune\Host\Ghost\Stdio\SGConsoleLogger;

require __DIR__ .'/../vendor/autoload.php';


$options = $argv;
array_shift($options);

$debug = in_array('-d', $options);
$reset = in_array('-r', $options);

// 设置
CommuneEnv::defineDebug($debug);
CommuneEnv::defineResetMind($reset);

$loop = Factory::create();
$stdio = new Stdio($loop);

$stdio->setPrompt('> ');


$config = [];
$app = new IGhost(
    new SGConfig(),
    null,
    null,
    null,
    new SGConsoleLogger(
        $stdio,
        true,
        CommuneEnv::isDebug() ? \Psr\Log\LogLevel::DEBUG : \Psr\Log\LogLevel::INFO
    )
);

// activate
$app->onFail([$stdio, 'end'])
    ->bootstrap()
    ->activate();

// connect event
$response = $app->handleRequest(new SGRequest(
    new Message\Host\Convo\IEventMsg(['eventName' => 'connected']),
    $stdio
));
$response->end();

// each message
$stdio->on('data', function($line) use ($app, $stdio) {

    $line = rtrim($line, "\r\n");
    $a = microtime(true);

    $request = new SGRequest(new Message\Host\Convo\IText($line), $stdio);
    $response = $app->handleRequest($request);

    $response->end();
    $b = microtime(true);

    $stdio->write('gap:'. round(($b - $a) * 1000000) . "\n");

});


$loop->run();
