<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Mind;
use Commune\Ghost\Blueprint\Dialog\Intend;
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
     * stage 的全程, 通常对应 IntentName
     * @return string
     */
    public function getFullname() : string;


    /*------- relations -------*/

    /**
     * 所属的 Context
     * @return ContextDef
     */
    public function contextDef() : ContextDef;

    /**
     * 作为 Intent 的匹配规则.
     * @return null|IntentDef
     */
    public function intentDef() : ? IntentDef;

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

    /*------- stage 启动 -------*/

    /**
     * 作为意图被命中时, 还未进入当前 Stage
     *
     * @param Intend $dialog
     * @return Operator
     */
    public function onIntend(
        Intend $dialog
    ) : Operator;

    /**
     * 正式进入 Stage 后
     * @return Operator
     */
    public function onStart(
    ) : Operator;


    /*------- wait -------*/

    /**
     * 没有命中任何分支, 由当前 Stage 自行响应.
     */
    public function onHear(
    ) : Operator;

    /*------- sleep 相关 -------*/

    /**
     * 当前 Thread 从 sleep 状态被唤醒时.
     */
    public function onWoke(
    ) : Operator ;


    /*------- depend 相关 -------*/

    /**
     * 依赖语境被拒绝时. 通常是因为权限不足.
     * @return Operator
     */
    public function onRejected(
    ) : Operator;

    /**
     * 当前 Thread 被用户要求 cancel 时
     * @return Operator
     */
    public function onCanceled(
    ) : Operator;

    /**
     * 依赖语境完成, 回调时.
     * @return Operator
     */
    public function onFulfilled(
    ) : Operator;

    /*------- 退出拦截 -------*/

    /**
     * Process 结束时, 会检查所有的 Thread 的态度.
     *
     * @return Operator
     */
    public function onQuit(
    ) : Operator ;

}