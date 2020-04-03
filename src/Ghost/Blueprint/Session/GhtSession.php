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

use Commune\Framework\Contracts\Cache;
use Commune\Framework\Contracts\Messenger;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Blueprint\Intercom;
use Commune\Ghost\Blueprint;
use Commune\Ghost\Contracts;
use Commune\Message\Blueprint\Message;
use Commune\Support\Babel\BabelResolver;
use SebastianBergmann\CodeCoverage\Driver\Driver;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * 以下内容可以依赖注入
 *
 * ## 容器
 *
 * @property-read ReqContainer $container                       请求级容器
 *
 * ## 请求
 *
 * @property-read Intercom\GhostInput $ghostInput
 * @property-read Scene $scene                                  场景信息
 * @property-read GhtSessionScope $scope                        本轮对话的作用域
 * @property-read Blueprint\Chat\Chat $chat
 * @property-read GhtSessionLogger $logger                      请求级日志
 *
 * ## Ghost 组件
 *
 * @property-read Blueprint\Ghost $ghost                        对话机器人的灵魂
 * @property-read Blueprint\Mind\Mindset $mind                  对话机器人的思维. 公共的
 * @property-read Blueprint\Meta\MetaRegistrar $metaReg         元数据的注册表
 *
 * ## 驱动类组件
 *
 * @property-read Driver $driver                                Session 的驱动, 读写各种数据.
 * @property-read Cache $cache                                  缓存
 * @property-read Messenger $messenger
 *
 * ## 功能组件
 *
 * @property-read Blueprint\Auth\Authority $authority
 * @property-read Blueprint\Memory\Memory $memory               机器人的记忆
 * @property-read Blueprint\Speak\Speaker $speaker
 *
 */
interface GhtSession
{
    /*------ silence ------*/

    /**
     * 让 Session 不做任何记录.
     */
    public function silence() : void;

    /*------ output ------*/

    /**
     * @param Message $message
     */
    public function output(Message $message) : void;

    /**
     * @param string $chatId
     * @param Message $message
     */
    public function deliver(string $chatId, Message $message) : void;

    /**
     * @return Intercom\GhostOutput[]
     */
    public function getOutputs() : array;

    /*------ event ------*/

    /**
     * 注册一个handler, 用于在 Ghost 不同生命周期的时候进行响应.
     * @param string $eventId
     * @param callable $handler
     */
    public function listen(string $eventId, callable $handler) : void;

    /**
     * 触发一个事件
     * @param Blueprint\Event\GhostEvent $event
     */
    public function fire(Blueprint\Event\GhostEvent $event) : void;

    /*------ life circle ------*/

    /**
     * 结束 Session, 处理垃圾回收
     */
    public function finish() : void;

    /**
     * @return bool
     */
    public function isFinished() : bool;
}