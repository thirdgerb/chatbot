<?php

namespace Commune\Chatbot\OOHost\Dialogue;

use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\History\History;
use Commune\Chatbot\OOHost\Session\Session;
use Psr\Log\LoggerInterface;


/**
 * 对话管理的根对象
 *
 * @property-read string $belongsTo
 * @property-read App $app 上下文中的 IoC 容器
 * @property-read Session $session
 * @property-read Redirect $redirect 上下文重定向的 Api
 * @property-read LoggerInterface $logger
 * @property-read History $history 上下文的管理者.
 *
 */
interface Dialog
{
    // 用这个做参数, 可以拿到所有依赖注入的键名.
    const DEPENDENCIES = 'dependencies';

    /*-------- 当前属性 --------*/

    /**
     * 当前对话要处理的消息.
     * @return Message
     */
    public function currentMessage() : Message;

    /**
     * 获取当前dialog 所在的 context
     * @return Context
     */
    public function currentContext() : Context;

    /**
     * 获取当前 dialog 所在的 context 的 stage
     * @return string
     */
    public function currentStage() : string;

    /**
     * 获取当前 dialog 已经提出的问题.
     * @return Question|null
     */
    public function currentQuestion() : ? Question;


    /*-------- 状态校验. --------*/

    /**
     * Dialog 是否属于某个Context的上下文.
     * 需要 currentContext()->getId() === $context->getId()
     *
     * @param Context $context
     * @return bool
     */
    public function isCurrent(Context $context) : bool;

    /**
     * 当前对话被另一个Context 所依赖.
     *
     * @return bool
     */
    public function isDepended() : bool;

    /**
     * 当前Context被哪个context所依赖.
     * @return null|string
     */
    public function isDependedBy() : ? string;

    /*-------- 创建一个对象 --------*/

    /**
     * 根据contextName 生成一个context 实例.
     *
     * @param string $contextName
     * @param array $args 按先后顺序传入参数.
     * @return Context
     * @throws \InvalidArgumentException
     */
    public function newContext(string $contextName, ...$args) : Context;

    /**
     * 在dialog 里找到一个 context实例, 前提是掌握了contextId
     * @param string $id
     * @return Context|null
     */
    public function findContext(string $id) : ? Context;

    /*-------- 消息 --------*/

    /**
     * 使用内置的方法发出格式化的消息.
     *
     * @param array $slots
     * @return DialogSpeech
     */
    public function say(array $slots = []) : DialogSpeech;

    /**
     * 向用户发出一个消息.
     * @param Message $message
     */
    public function reply(Message $message) : void;

    /**
     * 聆听一个消息并作出响应.
     * @param Message $message  为空则用 incomingMessage 默认的.
     * @return Hearing
     */
    public function hear(Message $message = null) : Hearing;

    ########## 导航类逻辑 ##########

    /*-------- 完成 --------*/

    /**
     * 触发下一个 stage, 没有的话调用fulfill
     *
     * @return Navigator
     */
    public function next() : Navigator;


    /*-------- context stage --------*/

    /**
     * 重新开始当前 context.
     * @return Navigator
     */
    public function restart() : Navigator;

    /**
     * 当前 context 进入下一个 stage
     *
     * @param string $stageName
     * @param bool $resetPipe   是否重置掉当前stage 回调的stage路径.
     * @return Navigator
     */
    public function goStage(
        string $stageName,
        bool $resetPipe = false
    ) : Navigator;

    /**
     * 重新 start 当前的 stage
     * @return Navigator
     */
    public function repeat() : Navigator;


    /**
     * 像管道一样经过若干个指定的 stage
     *
     * @param array $stages
     * @param bool $resetPipe   是否重置掉当前stage 回调的stage路径.
     * @return Navigator
     */
    public function goStagePipes(
        array $stages,
        bool $resetPipe = false
    ) : Navigator;


    /*-------- history --------*/

    /**
     * 当做什么都没发生过. 语境还原到上一轮对话状态.
     * 中间经历过语境跳转, 也会重置会上一轮对话时.
     *
     * 有问题的话, 只重复问题.
     *
     * 与repeat的区别在于, 如果这轮对话 stage从 a->b->c
     *
     * 然后执行 repeat, 所在的stage 是 c.
     * 如果执行 rewind, 则所在的是 a.
     *
     * @return Navigator
     */
    public function rewind() : Navigator;

    /**
     * 明确返回 miss match. 不需要经过 $dialog->hear()->end();
     * @return Navigator
     */
    public function missMatch() : Navigator;

    /**
     * 用同一个stage 等待用户的下一次消息.
     * @return Navigator
     */
    public function wait() : Navigator;

    /*-------- 退出语境 --------*/


    /**
     * 完成当前的任务, 向前回调.
     * @param bool $skipSelfExitingEvent
     * @return Navigator
     */
    public function fulfill(bool $skipSelfExitingEvent = false) : Navigator;


    /**
     * 退回到上一个向用户提出问题的状态.
     * @return Navigator
     */
    public function backward() : Navigator;


    /**
     * 退出当前的 session. 下次用户进来, 会从头开始对话.
     * @param bool $skipSelfExitingEvent
     * @return Navigator
     */
    public function quit(bool $skipSelfExitingEvent = false) : Navigator;


    /**
     * 机器人拒绝用户进入当前的语境.
     *
     * @param string|null $reason 为null 会使用默认的回复. 为空值, 则不做任何回复.
     * @param bool $skipSelfExitingEvent 决定是否被当前context 的 onReject 补获
     * @return Navigator
     */
    public function reject(string $reason = null, bool $skipSelfExitingEvent = false) : Navigator;

    /**
     * 用户主动取消当前的语境.
     *
     * @param bool $skipSelfExitingEvent
     * @return Navigator
     */
    public function cancel(bool $skipSelfExitingEvent = false) : Navigator;



    /*-------- subDialog 子会话. --------*/

    // 子会话是一个特殊的功能. 类似于 middleware 中间件.
    // 消息会先经过父会话, 然后进到子会话, 再回到父会话, 再往下走.

    // 父子会话拥有独立的生命周期, 独立的流程.
    // 父会话可以让子会话来处理消息. 子会话不能操作父会话.

    // 子会话的调度, 让父会话能感知的只有三种情况:
    // - quit
    // - missMatch
    // - wait

    // 父子会话并不相互通信. 它们的关系类似于中间件管道之间.
    // 如果一定要通信, 还是得通过共享的内存数据. 不过可能会有难以控制的情况.

    /**
     * 获取一个子对话. 可能存在着, 也可能要重新创建.
     *
     * @param string $belongsTo
     * @param callable $rootMaker
     * @param Message|null $message
     * @return SubDialog
     */
    public function getSubDialog(
        string $belongsTo,
        callable $rootMaker,
        Message $message = null
    ) : SubDialog ;

}