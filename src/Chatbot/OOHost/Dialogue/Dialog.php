<?php

namespace Commune\Chatbot\OOHost\Dialogue;

use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Session;
use Psr\Log\LoggerInterface;


/**
 * @property-read App $app
 * @property-read Session $session
 * @property-read Redirect $redirect
 * @property-read LoggerInterface $logger
 *
 */
interface Dialog
{
    // 用这个做参数, 可以拿到所有依赖注入的键名.
    const DEPENDENCIES = 'dependencies';

    /**
     * @return Context
     */
    public function currentContext() : Context;

    /**
     * @return string
     */
    public function currentStage() : string;

    /**
     * @return Question|null
     */
    public function prevQuestion() : ? Question;

    /**
     * 根据contextName 生成一个context 实例.
     *
     * @param string $contextName
     * @param array $args 按先后顺序传入参数.
     * @return Context
     * @throws \InvalidArgumentException
     */
    public function newContext(string $contextName, ...$args) : Context;

    /*-------- 消息 --------*/

    /**
     * 使用内置的方法发出格式化的消息.
     *
     * @param array $slots
     * @return Speech
     */
    public function say(array $slots = []) : Speech;

    /**
     * 向用户发出一个消息.
     * @param Message $message
     */
    public function reply(Message $message) : void;

    /**
     * 聆听一个消息并作出响应.
     * @param Message $message
     * @return Hearing
     */
    public function hear(Message $message) : Hearing;

    ########## 导航类逻辑 ##########

    /*-------- 完成 --------*/

    /**
     * 触发下一个 stage, 没有的话调用fulfill
     * @return Navigator
     */
    public function next() : Navigator;

    /**
     * 完成当前的任务, 向前回调.
     * @return Navigator
     */
    public function fulfill() : Navigator;


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
     * 退回到上一个向用户提出问题的状态.
     * @return Navigator
     */
    public function backward() : Navigator;

    /**
     * 重新向用户提出上一个问题
     * 而不是重复当前stage
     * @return Navigator
     */
    public function rewind() : Navigator;

    /**
     * 明确告诉用户 miss match. 然后执行 repeat
     * @return Navigator
     */
    public function missMatch() : Navigator;

    /**
     * 用同一个stage 等待用户的下一次消息.
     * @return Navigator
     */
    public function wait() : Navigator;

    /**
     * 退出当前的 session. 下次用户进来, 会从头开始对话.
     * @return Navigator
     */
    public function quit() : Navigator;


    /*-------- 异常 --------*/

    /**
     * 机器人拒绝用户进入当前的语境.
     *
     * @return Navigator
     */
    public function reject() : Navigator;

    /**
     * 用户主动取消当前的语境.
     *
     * @return Navigator
     */
    public function cancel() : Navigator;

}