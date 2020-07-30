<?php

use Commune\Platform\Libs;
use Commune\Blueprint\CommuneEnv;
use Commune\Host\IHost;

require __DIR__ . '/../../vendor/autoload.php';

// 设置
CommuneEnv::defineDebug(in_array('-d', $argv));
CommuneEnv::defineResetMind(in_array('-r', $argv));


// 启动异构的服务.
$config = include __DIR__ . '/../configs/host.php';

$host = new IHost($config);

return $host;




