<?php

use Commune\Ghost\IGhost;
use Commune\Blueprint\Configs\GhostConfig;
use React\EventLoop\Factory;
use Clue\React\Stdio\Stdio;

require __DIR__ .'/../vendor/autoload.php';


$config = [];
$app = null;
//
$app = new IGhost(new GhostConfig(), true);
$app->bootstrap()->activate();


$loop = Factory::create();
$stdio = new Stdio($loop);

$stdio->setPrompt('> ');

$stdio->on('data', function($line) use ($stdio, $app) {

    $line = rtrim($line, "\r\n");

    $stdio->write($line);
});


$loop->run();
