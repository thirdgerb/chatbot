<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */


use Commune\Platform\Libs;
use Commune\Blueprint\CommuneEnv;
use Clue\React\Stdio\Stdio;
use React\EventLoop\Factory;
use Commune\Protocals\HostMsg;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Protocals\HostMsg\DefaultEvents;
use Commune\Message\Host\Convo\IEventMsg;

require __DIR__ . '/../vendor/autoload.php';

// 定义方法

function createRequest(HostMsg $message) : GhostRequest
{
    $input = \Commune\Message\Intercom\IInputMsg::instance($message, 'test', 'test', 'test');
    return \Commune\Kernel\Protocals\IGhostRequest::instance('ghost', false, $input);
}

function renderResponse(Stdio $stdio, GhostResponse $response) : void
{
    $outputs = $response->getOutputs();
    foreach ($outputs as $output) {
        $stdio->write($output->getMsgText() . "\n");
    }
}


// 设置环境变量
CommuneEnv::defineDebug(in_array('-d', $argv));
CommuneEnv::defineResetMind(in_array('-r', $argv));

// 加载配置
$config = include __DIR__ . '/config/ghost/bare_ghost.php';

// 完成
$ghost = new \Commune\Ghost\IGhost($config);
$ghost->bootstrap()->activate();


$loop = Factory::create();
$stdio = new Stdio($loop);

$stdio->setPrompt('> ');

// 初始化连接
$event = IEventMsg::instance(DefaultEvents::EVENT_CLIENT_CONNECTION);
$request = createRequest($event);
$response = $ghost->handleRequest($request);
renderResponse($stdio, $response);

$stdio->on('data', function($line) use ($stdio, $ghost) {
    $message = \Commune\Message\Host\Convo\IText::instance($line);
    $request = createRequest($message);

    /**
     * @var \Commune\Blueprint\Kernel\Protocals\GhostResponse $response
     */
    $response = $ghost->handleRequest($request);
    renderResponse($stdio, $response);
});

$loop->run();

