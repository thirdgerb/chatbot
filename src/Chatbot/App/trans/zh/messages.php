<?php


return [
    'hello' => [
        'world'  => '您好,世界',
    ],

    'system' => [
        'platformNotAvailable' => '系统不可用',
        'chatIsTooBusy' => '输入太频繁',
        'systemError' => '系统错误',
    ],

    'dialog' => [
        'farewell' => '再见!',
        'missMatched' => '没有明白什么意思',
        'continue' => '输入 "." 继续',
        'script' => [
            'continue' => '输入 "." 继续, 输入".." 跳到最后'
        ]
    ],

    'command' => [
        'notExists' => '命令 %name% 不存在',
        'invalidArgument' => '参数 %name% 不正确',
        'notValid' => '%name% 不是合法的命令',
        'available' => "可用的命令: \n%available%",
        'contextNotExists' => 'context %contextName% 未注册',
        'navigateToContext' => '导航到 context %contextName%',
    ],

    'ask' => [
        'default' => '请输入 %name% (%default%)',
        'needs' => '您可能需要:',
        'needMore' => '您还有别的需要吗?',
        'continue' => '输入 "." 继续',
    ],

    'errors' => [
        'badAnswer' => '您输入的信息不正确, 请重新输入',
        'mustBeSupervisor' => '只有管理员允许访问当前语境',
    ],

];
