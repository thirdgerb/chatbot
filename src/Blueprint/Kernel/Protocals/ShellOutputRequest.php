<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Kernel\Protocals;

use Commune\Protocals\IntercomMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellOutputRequest extends AppRequest
{
    /**
     * 是否是异步请求.
     *
     * 异步请求可以通过 管道/广播 等方式来获取. 目前倾向于管道
     *
     * 异步请求的处理逻辑有二:
     *
     * 1. 从 MessageDB 中通过 BatchId 获取消息体. 异步请求是极简的, 不包含消息体.
     * 2. 要检查 SessionId 是否可发送. 有些端, 比如双通的端, 如果 SessionId 不可发送则丢弃.
     *
     * @return bool
     */
    public function isAsync() : bool;

    /**
     * @return IntercomMsg[]
     */
    public function getOutputs() : array;

    /**
     * @param IntercomMsg[] $messages
     */
    public function setOutputs(array $messages) : void;

    public function getCreatorId() : string;

    public function getCreatorName() : string;

    /**
     * @param int $errcode
     * @param string $errmsg
     * @return ShellOutputResponse
     */
    public function response(
        int $errcode = AppResponse::SUCCESS,
        string $errmsg = ''
    ) : ShellOutputResponse;
}