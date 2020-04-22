<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

require_once __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\Factory;
use Clue\React\Stdio\Stdio;

// 可以用于聊天室控制台的实验.

Swoole\Coroutine::set(['hook_flags'=> SWOOLE_HOOK_ALL]);
Swoole\Coroutine::create(function () {
    $loop = Factory::create();
    $stdio = new Stdio($loop);

    $stdio->setPrompt('>');

    $buffer = '';

    go(function() use (&$buffer){
        $i = 0;
        while(true) {
            Swoole\Coroutine::sleep(1);
            $buffer .= "async:$i\n";
            $i++;
        }
    });

    $loop->addPeriodicTimer(1, function() use ($stdio, &$buffer){
        $buffered = $buffer;
        $buffer = '';
        $stdio->write($buffered);
    });

    $stdio->on('data', function ($line) use ($stdio){
        $line = rtrim($line, "\r\n");
        $stdio->write("recv : $line \n");
    });

    $loop->run();
});
