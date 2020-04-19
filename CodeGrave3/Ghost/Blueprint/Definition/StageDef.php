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
     * 作为意图的名称. 有可能对应的意图和全名不一致
     * @return string
     */
    public function getIntentName() : string;

    /**
     * 所属 Context 的名称.
     * @return string
     */
    public function getContextName() : string;

    /*------- relations -------*/

    public function findContextDef(Conversation $conversation) : ContextDef;

    public function findIntentDef(Conversation $conversation) : ? IntentDef;


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
     * @param Stage\OnIntend $stage
     * @return Operator
     */
    public function onIntend(
        Stage\OnIntend $stage
    ) : Operator;

    /**
     * 正式进入 Stage 后
     *
     * @param Stage\OnActivate $stage
     * @return Operator
     */
    public function onActivate(
        Stage\OnActivate $stage
    ) : Operator;


    /**
     * 当前 Thread 从 sleep 或者 gc 状态被唤醒时.
     *
     * @param Stage\OnActivate $stage
     * @return Operator
     */
    public function onWake(
        Stage\OnActivate $stage
    ) : Operator ;

    /**
     * 当前 Thread 从 blocking 状态抢占成功时.
     *
     * @param Stage\OnActivate $stage
     * @return Operator
     */
    public function onRetain(
        Stage\OnActivate $stage
    ) : Operator;


    /*------- heed -------*/

    /**
     * 没有命中任何分支, 由当前 Stage 自行响应.
     *
     * @param Stage\OnHeed $stage
     * @return Operator
     */
    public function onHeed(
        Stage\OnHeed $stage
    ) : Operator;


    /*------- retrace -------*/

    /**
     * 依赖语境被拒绝时. 通常是因为权限不足.
     *
     * @param Stage\OnRetrace $stage
     * @return null|Operator
     */
    public function onReject(
        Stage\OnRetrace $stage
    ) : ? Operator;

    /**
     * 当前 Thread 被用户要求 cancel 时
     *
     * @param Stage\OnRetrace $stage
     * @return null|Operator
     */
    public function onCancel(
        Stage\OnRetrace $stage
    ) : ? Operator;

    /**
     * 依赖语境完成, 回调时.
     *
     * @param Stage\OnRetrace $stage
     * @return null|Operator
     */
    public function onFulfill(
        Stage\OnRetrace $stage
    ) : ? Operator;


    /**
     * Process 结束时, 会检查所有的 Thread 的态度.
     *
     * @param Stage\OnRetrace $stage
     * @return null|Operator
     */
    public function onQuit(
        Stage\OnRetrace $stage
    ) : ? Operator ;

}