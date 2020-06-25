<?php

Co\run(function () {
    $server = new  Swoole\Coroutine\Server('127.0.0.1', 9503);

    $server->set([
        'open_length_check' => true,
        'package_max_length' => 1024 * 1024,
        'package_length_type' => 'N',
        'package_length_offset' => 0,
        'package_body_offset' => 4,
    ]);

    $server->handle(function (Swoole\Coroutine\Server\Connection $conn) {
         
        $success = true;
        $i = 0;
        while($success) {
            //发送数据
            $success = $conn->send("hello $i\n");
            $i ++;
            if ($i > 15) {
                $i = 0;
            }
            co::sleep(0.1);
        }
        echo "close\n";
        $conn->close();
    });

    //开始监听端口
    $server->start();
});
