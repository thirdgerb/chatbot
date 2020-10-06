<?php

/**
 * 基于 Stdio 实现的单点 Shell
 */

require __DIR__ .'/bootstrap/autoload.php';


// 运行平台. 注意, 同步客户端不支持异步非阻塞!!!!!
$host->run('console');

