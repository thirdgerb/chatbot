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

use Commune\Ghost\Blueprint\Stage;
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
     * 可以路由到的 Context 内部的 Stage
     *
     * @return string[] 监听的 Stage 名称. 允许使用 * 作为通配符.
     */
    public function routingStages() : array;

    /**
     * @return string[]
     */
    public function comprehendPipes() : array;

    /*------- stage 启动 -------*/

    /**
     * 作为意图被命中时, 还未进入当前 Stage
     *
     * @param Stage\Intend $dialog
     * @return Operator
     */
    public function onIntend(
        Stage\Intend $dialog
    ) : Operator;

    /**
     * 正式进入 Stage 后
     *
     * @param Stage\Activate $dialog
     * @return Operator
     */
    public function onActivate(
        Stage\Activate $dialog
    ) : Operator;


    /*------- wait -------*/

    /**
     * 没有命中任何分支, 由当前 Stage 自行响应.
     *
     * @param Stage\Heed $dialog
     * @return Operator
     */
    public function onHeed(
        Stage\Heed $dialog
    ) : Operator;

    /*------- sleep 相关 -------*/

    /**
     * 当前 Thread 从 sleep 状态被唤醒时.
     *
     * @param Stage\Retrace $dialog
     * @return Operator
     */
    public function onWake(
        Stage\Retrace $dialog
    ) : Operator ;

    /*------- depend 相关 -------*/

    /**
     * 依赖语境被拒绝时. 通常是因为权限不足.
     *
     * @param Stage\Retrace $dialog
     * @return Operator
     */
    public function onReject(
        Stage\Retrace $dialog
    ) : Operator;

    /**
     * 当前 Thread 被用户要求 cancel 时
     *
     * @param Stage\Retrace $dialog
     * @return Operator
     */
    public function onCancel(
        Stage\Retrace $dialog
    ) : Operator;

    /**
     * 依赖语境完成, 回调时.
     *
     * @param Stage\Retrace $dialog
     * @return Operator
     */
    public function onFulfill(
        Stage\Retrace $dialog
    ) : Operator;


    /**
     * Process 结束时, 会检查所有的 Thread 的态度.
     *
     * @param Stage\Retrace $dialog
     * @return Operator
     */
    public function onQuit(
        Stage\Retrace $dialog
    ) : Operator ;

}