<?php

use Commune\Ghost\IGhost;
use Commune\Blueprint\Configs\GhostConfig;
use Commune\Host\Ghost\Stdio\SGRequest;
use Commune\Host\Ghost\Stdio\SGConsoleLogger;
use Commune\Blueprint\Framework\Request\AppResponse;
use React\EventLoop\Factory;
use Clue\React\Stdio\Stdio;
use Commune\Message;

require __DIR__ .'/../vendor/autoload.php';


$loop = Factory::create();
$stdio = new Stdio($loop);

$stdio->setPrompt('> ');


$config = [];
$app = new IGhost(
    new GhostConfig(),
    true,
    null,
    null,
    null,
    new SGConsoleLogger($stdio)
);
$app->onFail(function() use ($stdio){
        $stdio->end();
    })
    ->bootstrap()
    ->activate();

$stdio->on('data', function($line) use ($app, $stdio) {

    $line = rtrim($line, "\r\n");
    $a = microtime(true);

    $request = new SGRequest($line, $stdio, $app->getConsoleLogger());
    $response = $app->handle($request);

    $response->send();
    $b = microtime(true);

    $stdio->write('gap:'. round(($b - $a) * 1000000) . "\n");

});


$loop->run();
