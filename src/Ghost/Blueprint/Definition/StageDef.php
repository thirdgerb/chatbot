<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Definition;

use Commune\Ghost\Blueprint\Dialog;
use Commune\Ghost\Blueprint\Operator\Operator;

/**
 * Stage 的封装对象
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface StageDef
{

    /*------- properties -------*/

    /**
     * Stage 在 Context 内部的唯一ID
     * @return string
     */
    public function getName() : string;

    /**
     * stage 的全名, 通常对应 IntentName
     * @return string
     */
    public function getFullname() : string;

    /**
     * @return string
     */
    public function getIntentName() : string;

    /*------- relations -------*/

    /**
     * 所属的 Context
     * @return ContextDef
     */
    public function getContextDef() : ContextDef;

    /**
     * 作为 Intent 的匹配规则.
     * @return null|RoutingDef
     */
    public function getRoutingDef() : ? RoutingDef;

    /*------- stage 路由 -------*/

    /**
     * 在 wait 状态下, 可以重定向走的意图
     * @return string[]
     */
    public function routingIntents() : array;

    /**
     * 监听的意图. 可以接受前缀, 用 '*' 结尾
     * @return string[] 监听的意图名称.
     */
    public function routingStages() : array;

    /**
     * 使用什么组件来进行理解.
     * @return null|string
     */
    public function comprehension() : ? string;

    /*------- stage 启动 -------*/

    /**
     * 作为意图被命中时, 还未进入当前 Stage
     *
     * @param Dialog\Intend $dialog
     * @return Operator
     */
    public function onIntend(
        Dialog\Intend $dialog
    ) : Operator;

    /**
     * 正式进入 Stage 后
     *
     * @param Dialog\Start $dialog
     * @return Operator
     */
    public function onStart(
        Dialog\Start $dialog
    ) : Operator;


    /*------- wait -------*/

    /**
     * 没有命中任何分支, 由当前 Stage 自行响应.
     *
     * @param Dialog\Hear $dialog
     * @return Operator
     */
    public function onHear(
        Dialog\Hear $dialog
    ) : Operator;

    /*------- sleep 相关 -------*/

    /**
     * 当前 Thread 从 sleep 状态被唤醒时.
     *
     * @param Dialog\Wake $dialog
     * @return Operator
     */
    public function onWake(
        Dialog\Wake $dialog
    ) : Operator ;

    /*------- yield 相关 -------*/


    public function onRetain(
        Dialog\Retain $retain
    ) : Operator;

    public function onAsync(
        Dialog\Async $dialog
    ) : Operator;




    /*------- depend 相关 -------*/

    /**
     * 依赖语境被拒绝时. 通常是因为权限不足.
     *
     * @param Dialog\Retrace $dialog
     * @return Operator
     */
    public function onReject(
        Dialog\Retrace $dialog
    ) : Operator;

    /**
     * 当前 Thread 被用户要求 cancel 时
     *
     * @param Dialog\Retrace $dialog
     * @return Operator
     */
    public function onCancel(
        Dialog\Retrace $dialog
    ) : Operator;

    /**
     * 依赖语境完成, 回调时.
     *
     * @param Dialog\Retrace $dialog
     * @return Operator
     */
    public function onFulfill(
        Dialog\Retrace $dialog
    ) : Operator;


    /**
     * Process 结束时, 会检查所有的 Thread 的态度.
     *
     * @param Dialog\Retrace $dialog
     * @return Operator
     */
    public function onQuit(
        Dialog\Retrace $dialog
    ) : Operator ;

}