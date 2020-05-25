<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindDef;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Receive;
use Commune\Blueprint\Ghost\Dialog\Resume;
use Commune\Blueprint\Ghost\Dialog\Exiting;
use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Blueprint\Ghost\Operate\Operator;


/**
 * Stage 的封装对象
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface StageDef extends Def
{

    /*------- properties -------*/

    /**
     * Stage 在 Context 内部的唯一ID
     * @return string
     */
    public function getStageShortName() : string;

    /**
     * 所属 Context 的名称.
     * @return string
     */
    public function getContextName() : string;

    /**
     * 当前 Stage 是否是 Context 的第一个 Stage.
     * @return bool
     */
    public function isContextRoot() : bool;

    /**
     * 所有的 Stage 都可以作为一个意图.
     * 如果不配置规则, 则没有匹配意图的能力.
     *
     * @return IntentDef
     */
    public function asIntentDef() : IntentDef;


    /*------- intend to stage -------*/

    /**
     * 激活当前的 Stage.
     *
     * @param Activate $dialog
     * @return Operator|null
     */
    public function onActivate(Activate $dialog) : Operator;

    /**
     * 接受到用户消息时.
     *
     * @param Receive $dialog
     * @return Operator
     */
    public function onReceive(Receive $dialog) : Operator;

    /**
     * 当前 Stage 因为意图而被触发时.
     *
     * @param Dialog $prev
     * @param Dialog $current
     * @return Operator|null
     */
    public function onRedirect(Dialog $prev, Dialog $current) : ? Operator;

    /**
     * 当前 stage 恢复时.
     * @param Resume $dialog
     * @return Operator|null
     */
    public function onResume(Resume $dialog) : ? Operator;

    /**
     * 当 stage 依赖的语境退出时.
     * @param Exiting $dialog
     * @return Operator|null
     */
    public function onExit(Exiting $dialog) : ? Operator;


}