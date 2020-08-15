<?php

use Commune\Components\Predefined\Join\JoinLang;
use Commune\Shell\Render\StageEventRenderer;
use Commune\Protocals\HostMsg\DefaultIntents;


return [

    /*------- system -------*/

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
            'require' => "请输入 {attrName} :",
            'forbid' => "无权限访问当前功能",
            'yes' => '是',
            'no' => '否',

        ],
    ],


    /*------- suggestions -------*/

    DefaultIntents::GUEST_LOOP_NEXT => '继续',
    DefaultIntents::GUEST_LOOP_BREAK => '跳过',


    /*------- predefined -------*/

    // 预定义各种功能所需要的文本.
    'predefined' => [
        'join' => [
            'application' => '来自 {shell} 的用户 {userName} [{userId}] 申请加入当前会话. 请问是否允许?',

        ],

    ],

    JoinLang::ERROR_SESSION_NOT_FOUND => "目标会话不存在",
    JoinLang::ERROR_SESSION_SAME => "目标 session 不能和当前一致!",
    JoinLang::APPLY_ASK => "接到加入会话申请, 来自 {fromApp} 的用户 {userName}, 请问是否通过?",
    JoinLang::APPLY_CANCEL => '已忽略加入会话的申请.',
    JoinLang::APPLY_REJECT => '已拒绝加入会话的申请',
    JoinLang::APPLY_PROVE => '已通过加入会话的申请',
    JoinLang::REPLY_CANCELED => '得到通知, 加入会话申请被忽略',
    JoinLang::REPLY_PROVED => '得到通知, 加入会话申请得到通过. 接下来双方会话会同步.',
    JoinLang::REPLY_REJECTED => '得到通知, 加入会话申请被拒绝.',


    /*------- confuse -------*/

    DefaultIntents::SYSTEM_DIALOG_CONFUSE => '意图无法理解',
    DefaultIntents::SYSTEM_DIALOG_UNABLE => "语境 [{await}] 目前无法响应 [{matched}] 意图.",
    // 可以再根据 await, 当前 stage, 定义 stage 独特的 confuse 话术.
    DefaultIntents::SYSTEM_DIALOG_UNABLE => [
        'some_stage_fullname' => 'special reply',
    ],


    /*------- stage event -------*/

    StageEventRenderer::PREFIX => [
        // 退出事件默认的回复
        DefaultIntents::GUEST_NAVIGATE_CANCEL => '退出当前语境',
        // 退出事件根据 stage name 再定义回复.
        DefaultIntents::GUEST_NAVIGATE_CANCEL => [
            'some_stage_fullname' => 'special reply',
        ],
    ],
];

