<?php

/*
 * 测试专用的 Stdio 同步客户端.
 *
 * 在命令行会启动一个基于 Stdio 实现的对话界面.
 *
 * 通过它可以连接到 Tcp 实现的 Shell 端.
 *
 * Demo 版包含两种 Tcp Shell 服务端:
 * - DuplexShell : 双工 Shell 端. 可以双工地发送消息.
 * - SyncShell : 同步 Shell 端.
 */

use Swoole\Coroutine;
use Clue\React\Stdio\Stdio;
use React\EventLoop\Factory;

require __DIR__ .'/../vendor/autoload.php';



Coroutine::create(function() {

    $loop = Factory::create();
    $stdio = new Stdio($loop);

    $stdio->setPrompt('> ');

    // client connect
    $client = new Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
    if (!$client->connect('127.0.0.1', 9502, 0.5))
    {
        $stdio->end("connect failed. Error: {$client->errCode}\n");
        exit;
    }

    $client->send(' ');
    $output = $client->recv(0.5);

    $stdio->write(!empty($output) ? $output : "failed \n");

    // 处理同步请求.
    $stdio->on('data', function($line) use ($client, $stdio) {

        $client->send($line . ' ');
        $data = $client->recv(0.5);

        if ($data === false) {
            $stdio->write("error, false received");
            $client->close();
            $stdio->end('quit');
        }

        $stdio->write($data);
    });


    $loop->run();
});
