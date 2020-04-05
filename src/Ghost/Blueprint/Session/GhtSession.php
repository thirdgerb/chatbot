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

use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Blueprint\Session\SessionLogger;
use Commune\Framework\Blueprint\Session\SessionStorage;
use Commune\Framework\Contracts\Cache;
use Commune\Framework\Contracts\Messenger;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Blueprint\Intercom;
use Commune\Ghost\Blueprint;
use Commune\Ghost\Contracts\GhtRequest;
use Commune\Ghost\Contracts\GhtResponse;
use Commune\Ghost\Contracts\SessionDriver;
use Commune\Message\Blueprint\Message;


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
 * @property-read SessionLogger $logger                         请求级日志
 * @property-read GhtRequest $request
 * @property-read GhtResponse $response
 *
 * ## Ghost 组件
 *
 * @property-read Blueprint\Ghost $ghost                        对话机器人的灵魂
 * @property-read Blueprint\Mind\Mindset $mind                  对话机器人的思维. 公共的
 * @property-read Blueprint\Meta\MetaRegistrar $metaReg         元数据的注册表
 *
 * ## 驱动类组件
 *
 * @property-read SessionDriver $driver                         Session 的驱动, 读写各种数据.
 * @property-read Cache $cache                                  缓存
 * @property-read Messenger $messenger
 * @property-read SessionStorage $storage                       Session 缓存.
 *
 * ## 功能组件
 *
 * @property-read Blueprint\Auth\Authority $auth
 * @property-read Blueprint\Memory\Memory $memory               机器人的记忆
 * @property-read Blueprint\Speak\Speaker $speaker
 * @property-read Blueprint\Runtime\Runtime $runtime
 *
 */
interface GhtSession extends Session
{

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

    /**
     * @return Message[][]
     *  [
     *    chatId => [
     *         $message1,
     *         $message2,
     *      ]
     *  ]
     */
    public function getDelivery() : array;

}