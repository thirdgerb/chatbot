<?php

use Commune\Blueprint\CommuneEnv;
use Commune\Host\IHostConfig;
use Commune\Host\IHost;

require __DIR__ . '/../vendor/autoload.php';

$options = $argv;
array_shift($options);

$debug = !in_array('-p', $options);
$reset = in_array('-r', $options);

$platformName = end($options);

// 设置
CommuneEnv::defineDebug($debug);
CommuneEnv::defineResetMind($reset);


$config = include __DIR__ . '/configs/host.php';

$hostConfig = new IHostConfig($config);
$host = new IHost($hostConfig);

// 运行平台.
$host->run($platformName);
