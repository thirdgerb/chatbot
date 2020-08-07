<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\HostMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DefaultIntents
{
    /*----- system intents -----*/

    // 系统繁忙
    const SYSTEM_SESSION_BUSY = 'system.session.busy';
    // 会话退出
    const SYSTEM_SESSION_QUIT = 'system.session.quit';
    // 会话异常, 关闭会话.
    const SYSTEM_SESSION_FAIL = 'system.session.fail';
    // 同步会话, 将当前会话和目标会话合并.
    const SYSTEM_SESSION_SYNC = 'system.session.sync';
    // 请求错误
    const SYSTEM_REQUEST_FAILURE = 'system.request.fail';

    // 命令输入错误
    const SYSTEM_COMMAND_ERROR = 'system.command.error';
    // 命令列表
    const SYSTEM_COMMAND_LIST = 'system.command.list';
    // 命令不存在
    const SYSTEM_COMMAND_MISS = 'system.command.miss';
    // 命令介绍
    const SYSTEM_COMMAND_DESC = 'system.command.desc';

    // 当前语境正在 yield 状态
    const SYSTEM_DIALOG_YIELD = 'system.dialog.yield';
    // 语境内的逻辑无法理解用户的意图
    const SYSTEM_DIALOG_CONFUSE = 'system.dialog.confuse';
    // 语境内的逻辑无法响应用户的意图
    const SYSTEM_DIALOG_UNABLE = 'system.dialog.unable';

    // 要求用户输入一个属性的值
    const SYSTEM_DIALOG_REQUIRE = 'system.dialog.require';
    // 用户没有访问权限.
    const SYSTEM_DIALOG_FORBID = 'system.dialog.forbid';

    /*----- guest intents -----*/

    const GUEST_NAVIGATE_CANCEL = 'navigation.cancel';
    const GUEST_NAVIGATE_QUIT = 'navigation.quit';
    const GUEST_NAVIGATE_HOME = 'navigation.home';
    const GUEST_NAVIGATE_BACK = 'navigation.backward';
    const GUEST_NAVIGATE_REPEAT = 'navigation.repeat';
    const GUEST_NAVIGATE_RESTART = 'navigation.restart';

    const GUEST_ATTITUDES_AFFIRM = 'attitude.affirm';
    const GUEST_ATTITUDES_AGREE = 'attitude.agree';
    const GUEST_ATTITUDES_COMPLEMENT = 'attitude.complement';
    const GUEST_ATTITUDES_DENY = 'attitude.deny';
    const GUEST_ATTITUDES_DONT = 'attitude.dont';
    const GUEST_ATTITUDES_GREET = 'attitude.greet';
    const GUEST_ATTITUDES_THANKS = 'attitude.thanks';

    const GUEST_DIALOG_ORDINAL = 'dialogue.ordinal';
    const GUEST_DIALOG_RANDOM = 'dialogue.random';
    const GUEST_DIALOG_HELP = 'dialogue.help';

    const GUEST_LOOP_BREAK = 'loop.break';
    const GUEST_LOOP_NEXT = 'loop.next';
    const GUEST_LOOP_PREVIOUS = 'loop.previous';
    const GUEST_LOOP_REWIND = 'loop.rewind';

}