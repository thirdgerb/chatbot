<?php

use Swoole\Coroutine;
use Clue\React\Stdio\Stdio;
use React\EventLoop\Factory;

function main(string $host, string $port) {

    \Swoole\Runtime::enableCoroutine();

    $code = Coroutine\run(function() use ($host, $port){

        $loop = Factory::create();
        $stdio = new Stdio($loop);

        $stdio->setPrompt('> ');

        // client connect
        $client = new Coroutine\Client(SWOOLE_SOCK_TCP);
        if (!$client->connect($host, $port, 0.5))
        {
            echo "connect failed. Error: {$client->errCode}\n";
            return 1;
        }

        // 主动连接.
        $client->send(' ');
        $output = $client->recv();


        if (empty($output)) {
            $stdio->end("failed");
        } else {
            echo $output . "\n";
        }

        Coroutine::create(function () use ($client, $stdio){
            while(true) {
                $data = $client->recv();
                if (!empty($data)) {
                    echo $data . "\n" . $stdio->getPrompt();
                }
            }
        });


        // 处理同步请求.
        $stdio->on('data', function($line) use ($client, $stdio) {
            $success = $client->send($line . ' ');
            if (!$success) {
                $stdio->end("failed sent");
            }
        });

        $loop->run();

        return 0;
    });

    exit($code);


}
