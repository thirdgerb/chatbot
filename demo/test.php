<?php

$app = require __DIR__ . '/autoload.php';


$loop = \React\EventLoop\Factory::create();
$stdio = new \Clue\React\Stdio\Stdio($loop);

$stdio->getReadline()->setPrompt('> ');

$app->singleton(\Commune\Chatbot\Contracts\ServerDriver::class, function() use ($stdio){
    return new \Commune\Chatbot\Demo\Impl\ServerDriverDemo($stdio);
});

$stdio->on('data', function ($line) use ($app) {
    $line = rtrim($line, "\r\n");

    /**
     * @var \Commune\Chatbot\Contracts\ChatbotKernel $kernel
     */
    $kernel = $app->make(\Commune\Chatbot\Contracts\ChatbotKernel::class);
    $kernel->handle($line);
});

$loop->run();
