<?php

use Swoole\Coroutine;
use Clue\React\Stdio\Stdio;
use React\EventLoop\Factory;

function main(string $host, string $port) {

    Coroutine::set([
        'enable_coroutine' => true,
    ]);

    Coroutine\run(function() use ($host, $port){

        $loop = Factory::create();
        $stdio = new Stdio($loop);

        $stdio->setPrompt('> ');

        // client connect
        $client = new Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
        if (!$client->connect($host, $port, 0.5))
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
}
