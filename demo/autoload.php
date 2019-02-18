<?php

use Commune\Chatbot\Contracts\ChatbotKernel;
use Commune\Chatbot\Contracts\ChatDriver;
use Commune\Chatbot\Contracts\IdGenerator;
use Commune\Chatbot\Contracts\SessionDriver;
use Commune\Chatbot\Framework\Kernel;
use Commune\Chatbot\Framework\Chat\ChatPipe;
use Commune\Chatbot\Command\AnalyzerPipe;
use Commune\Chatbot\Framework\HostPipe;
use Commune\Chatbot\Demo\Impl\ChatDriverDemo;
use Commune\Chatbot\Contracts\ExceptionHandler;

require_once __DIR__ .'/../vendor/autoload.php';


$app = new \Illuminate\Container\Container();

$app->bind(\Illuminate\Contracts\Container\Container::class, $app);

$app->singleton(\Psr\Log\LoggerInterface::class, function(){
    $handler = new \Monolog\Handler\StreamHandler(__DIR__.'/../tmp.log');
    return new \Monolog\Logger('chatbot', [$handler]);
});

$app->singleton(\Commune\Chatbot\Contracts\ChatbotApp::class, \Commune\Chatbot\Demo\Impl\ChatbotAppDemo::class);

$app->singleton(ChatbotKernel::class, Kernel::class);
$app->singleton(ChatDriver::class, ChatDriverDemo::class);
$app->singleton(ExceptionHandler::class, \Commune\Chatbot\Demo\Impl\ExceptionHandlerDemo::class);
$app->singleton(IdGenerator::class, \Commune\Chatbot\Demo\Impl\IdGeneratorDemo::class);
$app->singleton(SessionDriver::class, \Commune\Chatbot\Demo\Impl\SessionDriverDemo::class);

$app->singleton(\Commune\Chatbot\Contracts\ChatbotApp::class, function($app) {
    return new \Commune\Chatbot\Demo\Impl\ChatbotAppDemo($app);
});


return $app;
