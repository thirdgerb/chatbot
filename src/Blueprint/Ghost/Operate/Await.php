<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Operate;

use Commune\Protocals\HostMsg\Convo\VerbalMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Await extends Finale
{
    /*------ 链式操作 ------*/

    public function listen(
        array $stageRoutes = [],
        array $contextRoutes = []
    ) : Await;

    /**
     * 设定当前会话多久之后过期.
     *
     * @param int $expire
     * @return Await
     */
    public function expire(int $expire) : Await;


    /*------ 消极等待 ------*/

    /**
     * 重置到上一轮的对话.
     * 可以发出消息, 或者不发出.
     *
     * @param bool $silent
     * @return Operator
     */
    public function rewind(bool $silent = false) : Operator;

    /**
     * 什么也没听见, 当本轮对话没有发生.
     * @return Operator
     */
    public function dumb() : Operator;

    /**
     * 主动强调无法理解当前对话.
     * 不会继续尝试 Wake 其它对话.
     * @return Operator
     */
    public function confuse() : Operator;

    /**
     * 退回到若干步之前.
     * @param int $step
     * @return Operator
     */
    public function backStep(int $step = 1) : Operator;

    /*-------- question ---------*/

    /**
     * @param string $query
     * @param array $suggestions
     * @param $defaultChoice
     * @param bool $withRoutes
     * @return Operator
     */
    public function askChoose(
        string $query,
        array $suggestions = [],
        $defaultChoice = 0,
        bool $withRoutes = true
    ) : Operator;

    /**
     * 要求客户端确认.
     *
     * @param string $query
     * @param bool $default
     * @return Operator
     */
    public function askConfirm(string $query, bool $default = true) : Operator;

    /**
     * 要求客户端回答跟 Entity 相关的值.
     * @param string $query
     * @param string $entityName
     * @return Operator
     */
    public function askEntity(
        string $query,
        string $entityName
    ) : Operator;

    /**
     * 要客户端回答任何问题.
     *
     * @param string $query
     * @param array $suggestions
     * @param string $messageType
     * @return Operator
     */
    public function askAny(
        string $query,
        array $suggestions = [],
        string $messageType = VerbalMsg::class
    ) : Operator;

    /**
     * 要求客户端输入指定类型的消息.
     * @param string $protocal
     * @return Await
     */
    public function askMessage(string $protocal) : Operator;

    /**
     * 要求客户端继续循环.
     * @param string $query
     * @param int $maxTurn
     * @return Operator
     */
    public function askLoop(
        string $query,
        int $maxTurn
    ) : Operator;
}