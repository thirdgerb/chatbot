<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Operator;


/**
 * 运行多轮对话逻辑时的算子.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Operator
{
    // 启动流程  6
    const MESSAGE_START = '';
    const MESSAGE_COMPREHEND = '';
    const MESSAGE_STAGES_ROUTING = '';
    const MESSAGE_INTENTS_ROUTING = '';
    const MESSAGE_HEAR = '';
    const MESSAGE_INTEND = '';

    // break 5
    const BREAK_BACK_STEP = '';
    const BREAK_REWIND = '';
    const BREAK_LISTEN = '';
    const BREAK_DUMB = '';
    const BREAK_QUIT = '';

    // hear 1
    const HEAR_CONFUSE = '';

    // stage 2
    const STAGE_START = '';
    const STAGE_RETAIN = '';

    // fallback 3
    const FALLBACK_FULFILL = '';
    const FALLBACK_CANCEL = '';
    const FALLBACK_HOME = '';

    // redirect 3
    const REDIRECT_YIELDING = '';
    const REDIRECT_DEPENDING = '';
    const REDIRECT_SLEEPING = '';

    // Async 回调 3
    const ASYNC_AWAIT = '';
    const ASYNC_RETAIN = '';
    const ASYNC_DROP = '';


    public function getName() : string;

    public function invoke() : ? Operator;
}