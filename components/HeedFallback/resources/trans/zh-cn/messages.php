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

    Lang::REQUIRE_INTENT_NAME => "请为对话内容选择一个意图:\n {text}\n\n",

    Lang::REQUIRE_SEARCH_INTENT => "请输入字符串搜索一个意图, 或作为新建意图 ID",

    Lang::REQUIRE_TRANS_REPLY => "请提供一个文字回复给用户",

    Lang::REQUIRE_INTENT_CREATE => '尝试创建意图 {intent}, 请为之拟定一个方便理解的标题: ',

    Lang::SELECTED_INTENT_NOT_FOUND => "找到不到指定的意图 {intent}, 或名称不合法",

    Lang::CONFIRM_INTENT => "确认使用意图名 {selected}: ",

    Lang::STRATEGY_CHOOSE_SCOPE => '请为上下文语义相关回复策略选择一个作用域: ',

    Lang::STRATEGY_SCOPE_CONTEXT => "上下文语境 (context) 相关",

    Lang::STRATEGY_SCOPE_CONTEXT => "当前对话场景 (stage) 相关",

    Lang::STRATEGY_SCOPE_INTENT => "仅意图相关",

    Lang::STRATEGY_CHOOSE => '请给 id 为 {id} 的对话场景选择一个回复策略: ',

    Lang::REPLY_IS_PREPARING => "sorry, 回复内容还在准备中... ",

    Lang::STRATEGY_LEARNED => "您之前说过的 \"{text}\", 我刚学会了如何回复. 感谢!",

    Lang::STRATEGY_EXISTS => "当对话语境在 {await}, 意图为 {intent} 时, 回复策略已经确定. id 为 {id}.",
];