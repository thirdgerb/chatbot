<?php


use Commune\Blueprint\CommuneEnv;
use Commune\Host\IHost;

require __DIR__ . '/../../../vendor/autoload.php';

$platformName = $argv[1] ?? '';
if (empty($platformName)) {
    echo "platform should not be empty \n";
    exit(1);
}

// 设置
CommuneEnv::defineDebug(true);
CommuneEnv::defineResetMind(true);


// 启动异构的服务.
$hostConfig = new \Commune\Demo\Heterogeneous\HeHostConfig([
]);

$host = new IHost($hostConfig);

// 运行平台.
$host->run($platformName);

