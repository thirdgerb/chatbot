<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;

use Commune\Blueprint\NLU\NLUManager;
use Commune\Blueprint\Exceptions\CommuneRuntimeException;
use Commune\Blueprint\Ghost;
use Commune\Contracts\Cache;
use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Blueprint\Framework\Auth\Authority;
use Commune\Contracts\Messenger\Broadcaster;
use Commune\Kernel\ClonePipes\CloneLockerPipe;
use Commune\Protocols\Comprehension;
use Commune\Protocols\HostMsg;
use Commune\Protocols\Intercom\InputMsg;
use Commune\Protocols\Intercom\OutputMsg;
use Commune\Protocols\IntercomMsg;
use Commune\Support\Registry\OptRegistry;
use Commune\Blueprint\Ghost\Cloner\ClonerDispatcher;


/**
 *
 * Ghost 的分身, 多轮对话的管理器.
 * 核心方法:
 *
 * $cloner->runDialogManage($operator = null);
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * # 作用域
 * @property-read GhostConfig $config               机器人配置
 *
 * # Session
 * @property-read Cloner\ClonerLogger $logger       日志
 * @property-read Cloner\ClonerStorage $storage     Session 级 Storage
 *
 * # 容器
 * @property-read ReqContainer $container           容器
 *
 * # 请求相关
 *
 * ## 输入信息
 * @property-read InputMsg $input                   输入
 * @property-read Comprehension $comprehension      理解模块
 * @property-read Ghost\Tools\Matcher $matcher      默认的匹配逻辑工具
 *
 * ## 环境信息
 * @property-read Cloner\ClonerScene $scene         场景信息. 代表请求中与消息无关的信息.
 * @property-read Cloner\ClonerScope $scope         当前请求的维度.
 * @property-read Cloner\ClonerAvatar $avatar       当前分身的形象数据.
 * @property-read Ghost\Cloner\ClonerEnv $env       当前请求的环境变量
 * @property-read Ghost\Cloner\ClonerGuest $guest   当前请求的用户信息
 *
 * # 自然语言模块
 *
 * @property-read NLUManager $nlu                   自然语言理解模块
 *
 * # 功能组件
 * @property-read Cache $cache                      公共缓存
 * @property-read Authority $auth                   授权模块
 * @property-read ClonerDispatcher $dispatcher      异步任务的工具类.
 *
 * # 复杂对话逻辑组件
 *
 * @property-read Ghost\Mindset $mind               机器人的思维
 * @property-read Runtime $runtime                  机器人的运行状态
 *
 * # Host 组件
 * @property-read Ghost $ghost                      Ghost 本体
 * @property-read OptRegistry $registry             配置注册表
 *
 */
interface Cloner extends Session
{

    /*----- status -----*/

    /**
     * 将当前会话设置为无状态请求
     */
    public function noState() : void;

    /**
     * 是否是无状态的 session
     * @return bool
     */
    public function isStateless() : bool;

    /*----- conversation -----*/

    /**
     * 每个分身拥有一个独立的 Session
     * 可以是和一个人对话, 也可以是同时和多人对话.
     * Session 掌握了通信渠道.
     *
     * @return string
     */
    public function getSessionId(): string;

    /**
     * 每个分身可以同时进行多个对话, 同时又分享对话的记忆.
     * 例如在群聊场景中, 每个用户都在分别和这个分身对话.
     *
     * ConversationId 用来标记不同的子会话.
     *
     * @return string
     */
    public function getConversationId() : string;

    /**
     * 退出当前多轮对话.
     */
    public function endConversation() : void;

    /**
     * 判断当前多轮对话是否已经结束.
     * @return bool
     */
    public function isConversationEnd() : bool;

    /**
     * 设置当前 Conversation 多轮对话不记录状态 (主要是 Runtime), 但不影响 Session
     */
    public function noConversationState(): void;

    /**
     * 判断当前是否是一个子对话.
     * 一个 Session 仅仅有一个主对话.
     * @return bool
     */
    public function isSubConversation() : bool;
    /**
     * @param string $sessionId
     * @return bool
     */
    public function isClonerExists(string $sessionId) : bool;

    /*----- 触发理解 -----*/

    /**
     * 运行多轮对话逻辑.
     * 默认从 OStart 开始
     * @see \Commune\Ghost\IOperate\OStart
     *
     * @param Operate\Operator|null $start
     * @throws CommuneRuntimeException
     */
    public function runDialogManager(Ghost\Operate\Operator $start = null) : void;

    /*----- 手动输出 -----*/

    /**
     * 设置是否拒绝接受输入消息.
     * 这样尽管经过了对话流程, 却不会把机器人生产的消息发送出去
     * @param bool $silent
     */
    public function silence(bool $silent = true) : void;

    /**
     * 输出消息.
     * @param OutputMsg $output
     * @param OutputMsg[] $outputs
     */
    public function output(OutputMsg $output, OutputMsg ...$outputs) : void;

    /**
     * 获得所有的输出消息.
     * @return IntercomMsg[]
     */
    public function getOutputs() : array;

    /**
     * 提交一个异步输入消息.
     *
     * @param InputMsg $input
     * @param InputMsg ...$inputs
     */
    public function asyncInput(InputMsg $input, InputMsg ...$inputs) : void;

    /**
     * 获取异步的输入消息
     * @return InputMsg[]
     */
    public function getAsyncInputs() : array;

    /**
     * 提交一个异步的投递消息, 会投递到目标 sessionId, 然后广播出去.
     * @param InputMsg $input
     * @param InputMsg ...$inputs
     */
    public function asyncDeliver(InputMsg $input, InputMsg ...$inputs) : void;

    /**
     * 获取所有的异步投递消息
     * @return InputMsg[]
     */
    public function getAsyncDeliveries() : array;


    /**
     * 基于 broadcaster 的广播机制.
     * 可以把一组消息作为事件来进行广播.
     *
     * Shells 可以根据自己业务形态来决定是否监听它.
     * 详见 @see Broadcaster
     *
     * 如果把 ShellId 作为 EventGroup
     * 把 ShellSessionId 作为 EventName 来广播,
     * 则可以把消息投递给指定 shell 的指定 session.
     *
     * 用这种策略可以把机器人作为一个云端通知工具, 将收到的消息推送给指定的客户端.
     *
     * @param string $eventGroup
     * @param string $eventName
     * @param string|null $creatorId            默认 avatar id
     * @param string|null $creatorName          默认 avatar name
     * @param HostMsg $message
     * @param HostMsg ...$messages
     */
    public function broadcast(
        string $eventGroup,
        string $eventName,
        ? string $creatorId,
        ? string $creatorName,
        HostMsg $message,
        HostMsg ...$messages
    ) : void;

    /*----- clone locker -----*/

    /**
     * 锁定一个 session + conversation 用于禁止并发通讯.
     * 机器人是有状态的, 因此需要有一个锁防止并发逻辑导致 "裂脑" 现象.
     * 然而机器人本身也有各种机制执行并发任务. 包括:
     *
     * 1. stateless request : 无状态请求. 则不会保存状态, 从而也不需要锁.
     * 2. 多进程. 每个 ConversationId 对应一个子进程, 共享 session. 从而可以分裂逻辑.
     * 3. async request : 异步请求会进入一个队列, 等到解锁后才执行.
     *
     * 具体实现 @see CloneLockerPipe
     *
     * Lock 的粒度是 Conversation.
     *
     * @param int $second
     * @return bool
     */
    public function lock(int $second) : bool;

    /**
     * @return bool
     */
    public function isLocked() : bool;

    /**
     * 解锁一个机器人的分身 + conversation. 允许通讯.
     * @return bool
     */
    public function unlock() : bool;

    /**
     * 判断 Cloner 的某一个组件是否已经实例化了.
     *
     * @param $name
     * @return bool
     */
    public function isSingletonInstanced($name) : bool;

}