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

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
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

//    /**
//     * 所属的 Context
//     * @return ContextDef
//     */
//    public function getContextDef() : ContextDef;

    public function getContextName() : string;

    /**
     * 作为 Intent 的匹配规则.
     * @return null|RoutingDef
     */
    public function getRoutingDef() : ? RoutingDef;

    /*------- stage 路由 -------*/

    /**
     * 在 wait 状态下, 可以跳转直达的 Context 名称.
     * 允许用 * 作为通配符.
     *
     * @param Conversation $conversation
     * @return string[]
     */
    public function contextRoutes(Conversation $conversation) : array;

    /**
     * 在 wait 状态下, 可以跳转直达的 Context 内部 Stage 的名称.
     * 允许用 * 作为通配符.
     *
     * @param Conversation $conversation
     * @return string[]
     */
    public function stageRoutes(Conversation $conversation) : array;

    /**
     * 当前 Stage 自定义的理解管道.
     * @param Conversation $conversation
     * @return string[]
     */
    public function comprehendPipes(Conversation $conversation) : array;

    /*------- intend to stage -------*/

    /**
     * 作为意图被命中时, 还未进入当前 Stage
     *
     * @param Stage\Intend $stage
     * @return Operator
     */
    public function onIntend(
        Stage\Intend $stage
    ) : Operator;

    /**
     * 正式进入 Stage 后
     *
     * @param Stage\Activate $stage
     * @return Operator
     */
    public function onActivate(
        Stage\Activate $stage
    ) : Operator;


    /**
     * 当前 Thread 从 sleep 状态被唤醒时.
     *
     * @param Stage\Activate $stage
     * @return Operator
     */
    public function onWake(
        Stage\Activate $stage
    ) : Operator ;



    /*------- heed -------*/

    /**
     * 没有命中任何分支, 由当前 Stage 自行响应.
     *
     * @param Stage\Heed $stage
     * @return Operator
     */
    public function onHeed(
        Stage\Heed $stage
    ) : Operator;


    /*------- retrace -------*/

    /**
     * 依赖语境被拒绝时. 通常是因为权限不足.
     *
     * @param Stage\Retrace $stage
     * @return Operator
     */
    public function onReject(
        Stage\Retrace $stage
    ) : Operator;

    /**
     * 当前 Thread 被用户要求 cancel 时
     *
     * @param Stage\Retrace $stage
     * @return Operator
     */
    public function onCancel(
        Stage\Retrace $stage
    ) : Operator;

    /**
     * 依赖语境完成, 回调时.
     *
     * @param Stage\Retrace $stage
     * @return Operator
     */
    public function onFulfill(
        Stage\Retrace $stage
    ) : Operator;


    /**
     * Process 结束时, 会检查所有的 Thread 的态度.
     *
     * @param Stage\Retrace $stage
     * @return Operator
     */
    public function onQuit(
        Stage\Retrace $stage
    ) : Operator ;

}