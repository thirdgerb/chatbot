<?php

use Commune\Components\HeedFallback\Constants\HeedFallbackLang as Lang;

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

return [

    Lang::PLEASE_TEACH_ME => '对于您的要求我还不知道如何响应, 请教我.',

    Lang::TEACH_TASK_NOT_FOUND => '教学任务 {batchId} 不见了!!',

    Lang::CHAT_MODULE_NOT_FOUND => '没有找到已注册的闲聊模块',

    Lang::FALLBACK_SCENE_BRIEF => "对话地址:{await}\n对话内容: {text}\n\n对话所在语境: {context}\n所在场景: {stage}\n命中意图: {matched}",

    Lang::REQUIRE_OPERATION => '请选择操作:',

    Lang::REQUIRE_DIRECT_REPLY => '请输入一个给用户的直接回复:',

    Lang::SIMPLE_CHAT_REPLY => "闲聊模块的回复是: {reply}",

    Lang::SIMPLE_CHAT_CONFIRM => "请确认使用默认回复, 或者输入一个句子教机器人回复: ",

    Lang::TOTAL_TASKS => "现有的教学任务总数是: {count}",

    Lang::CONFIRM_START_TASK => "是否开始一个教学课程?",

    Lang::TASK_DISAPPEAR => "oh no! 目标任务消失了!",
];