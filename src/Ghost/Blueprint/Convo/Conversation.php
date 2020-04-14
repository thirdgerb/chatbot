<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Convo;

use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Contracts\Cache;
use Commune\Framework\Contracts\Messenger;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Blueprint\Intercom;
use Commune\Ghost\Blueprint;
use Commune\Ghost\Contracts\GhostRequest;
use Commune\Ghost\Contracts\GhostResponse;
use Commune\Ghost\Contracts\Driver;
use Commune\Message\Blueprint\Message;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Context\Context;


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
 * @property-read Blueprint\Cloner\Cloner $cloner
 * @property-read ConversationLogger $logger                         请求级日志
 * @property-read GhostRequest $request
 * @property-read GhostResponse $response
 *
 * ## Ghost 组件
 *
 * @property-read Blueprint\Ghost $ghost                        对话机器人的灵魂
 * @property-read Blueprint\Mind\Mindset $mind                  对话机器人的思维. 公共的
 * @property-read Blueprint\Meta\MetaRegistrar $metaReg         元数据的注册表
 *
 * ## 驱动类组件
 *
 * @property-read Driver $driver                         Session 的驱动, 读写各种数据.
 * @property-read Cache $cache                                  缓存
 * @property-read Messenger $messenger
 *
 * ## 功能组件
 *
 * @property-read Blueprint\Auth\Authority $auth
 * @property-read Blueprint\Memory\Memory $memory               机器人的记忆
 * @property-read Blueprint\Speak\Speaker $speaker
 * @property-read Blueprint\Runtime\Runtime $runtime
 *
 */
interface Conversation extends Session
{

    /*------ properties ------*/

    /**
     * 机器人的唯一身份 ID
     * @return string
     */
    public function getCloneId() : string;


    /*------ ghost logic ------*/

    /**
     * 运行机器人的逻辑.
     *
     * @param Operator|null $operator
     */
    public function runDialogManager(Operator $operator = null) : void;

    public function newContext(string $contextName, array $entities) : Context;

    /*------ deliver ------*/

    /**
     * 投递回复消息给指定的 shellId
     *
     * Messenger::sendOutputs()
     *
     * @param string $shellName
     * @param string $shellId
     * @param Message $message
     */
    public function deliverOutput(string $shellName, string $shellId, Message $message) : void;

    /**
     * 投递输入消息给指定的 shellId
     *
     * Messenger::pushInput()
     *
     * @param string $shellName
     * @param string $shellId
     * @param Message $message
     */
    public function deliverInput(string $shellName, string $shellId, Message $message) : void;


    /*------ output ------*/

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

    public function goThroughPipes(array $pipes, string $via) : Conversation;
}