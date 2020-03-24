<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Session;

use Commune\Chatbot\Contracts\Cache;
use Commune\Chatbot\Contracts\Messenger;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Ghost\Blueprint\Ghost;
use Commune\Ghost\Blueprint\Memory\Memory;
use Commune\Ghost\Blueprint\Runtime\Runtime;
use Commune\Ghost\Contracts\GhtRequest;
use Commune\Ghost\Contracts\GhtResponse;
use Commune\Message\Internal\IncomingMsg;
use Commune\Message\Internal\OutgoingMsg;
use Commune\Message\Internal\Scope;
use Commune\Message\Message;
use SebastianBergmann\CodeCoverage\Driver\Driver;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * 以下内容可以依赖注入
 *
 * @property-read Ghost $ghost                      对话机器人的灵魂
 * @property-read ReqContainer $container           请求级容器
 * @property-read Scope $scope                      本轮对话的作用域
 * @property-read GhtRequest $request               本轮对话的同步请求
 * @property-read GhtResponse $response             本轮对话的同步响应
 * @property-read IncomingMsg $incoming             输入的消息
 * @property-read GhtSessionLogger $logger          请求级日志
 * @property-read Driver $driver                    Session 的驱动, 读写各种数据.
 *
 * 请求级单例
 *
 * @property-read Cache $cache                  缓存
 * @property-read Messenger $messenger
 *
 * 多轮对话相关模块
 *
 * @property-read Memory $memory                    机器人的记忆
 * @property-read Runtime $runtime                  上下文状态
 *
 */
interface GhtSession
{

    /**
     * @param OutgoingMsg[] $replies
     */
    public function reply(array $replies) : void;

    /**
     * @return OutgoingMsg[]
     */
    public function getReplies() : array;


    /**
     * @param string $policy
     * @return Message|null
     */
    public function allow(string $policy) : ? Message;

    /**
     * 结束 Session, 处理垃圾回收
     */
    public function finish() : void;
}