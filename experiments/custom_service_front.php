<?php


# 客服平台的一个简单的例子.
# 假设 客服是一个双工的通道, 例如 websocket.
# 可以选择用一个 "sub:chan" 协议来监听目标 chan 的广播

//创建Server对象，监听 127.0.0.1:9501端口
$serv = new Swoole\Server("127.0.0.1", 9501); 

$table = new Swoole\Table(1024);
$table->column('fd', Swoole\Table::TYPE_INT, 8);
$table->create();

$serv->table = $table;

$process = new Swoole\Process(function() use ($serv) {

    Co\run(function () use ($serv){
        $client = new Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
        if (!$client->connect('127.0.0.1', 9503, 0.5))
        {
            echo "connect failed. Error: {$client->errCode}\n";
        }

        while(true) {
            $data = $client->recv(-1);
            $data = trim($data);
            $arr = explode(' ', $data);
            list($text, $chan) = $arr;
            if ($serv->table->exists($chan)) {
                $fd = $serv->table->get($chan, 'fd');
                if ($serv->exists($fd)) {
                    $serv->send($fd, $data);
                } else {
                    $serv->table->del($chan);
                }
            }
        }

        $client->close();
    });
});

$serv->addProcess($process);

//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {  
    echo "Client: Connect.\n";
});

//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data){

    $data = trim($data);
    if (strpos($data, 'sub:') === 0 ) {
        $arr = explode(':', $data, 2);
        if (!empty($arr[1])) {
            $sub = $arr[1];
            $serv->table->set($sub, ['fd' => $fd]);
        }

    } 
    $serv->send($fd, "Server: $data\n");
});

//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start(); 
