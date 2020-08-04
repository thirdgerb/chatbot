<?php


return [

    'system' => [
        'command' => [
            'list' => "当前可用命令:\n{cmdList}",
            'error' => "命令 {command} 格式错误: \n {error}",
            'miss' => "命令 {command} 不存在!",
            'desc' => "{command}: {desc}\n{arguments}\n{options}"
        ],

        'session' => [
            'busy' => "机器人正忙",
            'quit' => "退出会话, 再见!",
            'fail' => "系统异常",
        ],

        'request' => [
            'fail' => "请求异常, 请重试",
        ],

        'dialog' => [
            'yield' => "任务在等待中",
            'confuse' => "意图无法理解",
            'require' => "请输入 {attrName} :",
            'forbid' => "无权限访问当前功能",
            'yes' => '是',
            'no' => '否',

        ],

    ],

    // 预定义各种功能所需要的文本.
    'predefined' => [
        'join' => [
            'application' => '来自 {shell} 的用户 {userName} [{userId}] 申请加入当前会话. 请问是否允许?',

        ],

    ],
];

