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


CommuneEnv::defineDebug(true);
CommuneEnv::defineResetMind(true);



$loop = Factory::create();
$stdio = new Stdio($loop);

$stdio->setPrompt('> ');


$config = [];
$app = new IGhost(
    new SGConfig(),
    null,
    null,
    null,
    new SGConsoleLogger($stdio)
);
$app->onFail([$stdio, 'end'])
    ->bootstrap()
    ->activate();

$stdio->on('data', function($line) use ($app, $stdio) {

    $line = rtrim($line, "\r\n");
    $a = microtime(true);

    $request = new SGRequest($line, $stdio);
    $response = $app->handle($request);

    $response->end();
    $b = microtime(true);

    $stdio->write('gap:'. round(($b - $a) * 1000000) . "\n");

});


$loop->run();
