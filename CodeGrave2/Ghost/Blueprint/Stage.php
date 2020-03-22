<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Stage
{
    // 作为意图, 被命中时
    const ON_INTEND = 'onIntend';

    // 正常启动
    const ON_START = 'onStart';

    // 收到用户消息
    const ON_HEAR = 'onHear';

    // 从 block 状态回来, 抢占了主题
    const ON_RETAIN = 'onRetain';

    // 回调类事件
    const ON_WAKE = 'onWake';
    const ON_CANCEL = 'onCancel';
    const ON_FULFILL = 'onFulfill';
    const ON_QUIT = 'onQuit';

    /**
     * 当前 Stage 所处的状态
     * @return string
     */
    public function getState() : string;

    public function isState(string $stage) : bool;
}