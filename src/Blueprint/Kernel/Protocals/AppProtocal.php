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

use Commune\Support\Protocal\Protocal;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface AppProtocal extends Protocal
{

    /**
     * 调用链条的 traceId, 方便排查跨平台的微服务调用链.
     * @return string
     */
    public function getTraceId() : string;

    /**
     * 请求响应的批次 ID, 要求时间有序的 UUID
     * 用于统筹每一帧响应的所有输入输出信息.
     *
     * @return string
     */
    public function getBatchId() : string;

    /**
     * 对于 Request 而言, 是处理消息的 SessionId
     * 对于 Response 而言, 关系到投递消息的目标 Session
     *
     * @return string
     */
    public function getSessionId() : string;

}