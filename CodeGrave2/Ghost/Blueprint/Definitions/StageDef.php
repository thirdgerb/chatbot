<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Definitions;

use Commune\Ghost\Blueprint\Context\CallbackStage;
use Commune\Ghost\Blueprint\Context\CancelStage;
use Commune\Ghost\Blueprint\Context\HearStage;
use Commune\Ghost\Blueprint\Context\QuitStage;
use Commune\Ghost\Blueprint\Context\StartStage;
use Commune\Ghost\Blueprint\Context\WokeStage;
use Commune\Ghost\Blueprint\Routing\Route;

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
    public function name() : string;

    /**
     * stage 的全程, 通常对应 IntentName
     * @return string
     */
    public function fullname() : string;


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


    /**
     * 指定的 NLUService. 不存在则使用默认的.
     * @return null|string
     */
    public function nluService() : ? string;

    /*------- stage 路由 -------*/

    # 各种状态的对应关系
    # wait -> heard
    # sleep -> woke
    # depend -> intended
    # intending
    # yield -> block

    /**
     * 作为意图被命中时, 还未进入当前 Stage
     *
     * @return Route
     */
    public function onIntending(
    ) : Route;

    /**
     * 正式进入 Stage 后
     * @return Route
     */
    public function onStart(
        StartStage $stage
    ) : Route;



    /**
     * 没有命中任何分支, 由当前 Stage 自行响应.
     */
    public function onHear(
        HearStage $stage
    ) : Route;

    /*------- 语境回调 -------*/

    /**
     * 当前 Thread 从 sleep 状态被唤醒时.
     */
    public function onWoke(
        WokeStage $stage
    ) : Route ;


    /*------- 退出拦截 -------*/

    /**
     * 当前 Thread 被动 cancel 时
     * @return Route
     */
    public function onCanceled(
        CancelStage $stage
    ) : Route ;

    /**
     * Process 结束时, 会检查所有的 Thread 的态度.
     *
     * @return Route
     */
    public function onQuit(
        QuitStage $stage
    ) : Route ;

}