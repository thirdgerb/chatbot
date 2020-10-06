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

require __DIR__ . '/../vendor/autoload.php';

// 设置
CommuneEnv::defineDebug(in_array('-d', $argv));
CommuneEnv::defineResetMind(in_array('-r', $argv));


// 启动异构的服务.
$config = include __DIR__ . '/config/ghost/bare_ghost.php';

$ghost = new \Commune\Ghost\IGhost($config);
$ghost->bootstrap()->activate();


$loop = Factory::create();
$stdio = new Stdio($loop);

$stdio->setPrompt('> ');


$stdio->on('data', function($line) use ($stdio, $ghost) {
    $message = \Commune\Message\Host\Convo\IText::instance($line);
    $input = \Commune\Message\Intercom\IInputMsg::instance($message, 'test', 'test', 'test');
    $request = \Commune\Kernel\Protocals\IGhostRequest::instance('ghost', false, $input);

    /**
     * @var \Commune\Blueprint\Kernel\Protocals\GhostResponse $response
     */
    $response = $ghost->handleRequest($request);
    $outputs = $response->getOutputs();

    foreach ($outputs as $output) {
        $stdio->write($output->getMsgText() . "\n");
    }
});

$loop->run();

