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
use Commune\Blueprint\Ghost\Dialog\Retain;
use Commune\Blueprint\Ghost\Dialog\Withdraw;
use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Blueprint\Ghost\Runtime\Operator;


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
    public function getStageName() : string;

    /**
     * 所属 Context 的名称.
     * @return string
     */
    public function getContextName() : string;

    /**
     * @return bool
     */
    public function isContextRoot() : bool;

    /**
     * @return IntentDef|null
     */
    public function asIntentDef() : ? IntentDef;


    /*------- intend to stage -------*/

    /**
     * @param Dialog $prev
     * @param Dialog $current
     * @return Operator|null
     */
    public function onIntercept(Dialog $prev, Dialog $current) : ? Operator;

    /**
     * 激活当前的 Stage, 然后等待回调.
     * @param Activate $dialog
     * @return Operator
     */
    public function onActivate(Activate $dialog) : Operator;

    /**
     * 接收到一个用户消息时.
     *
     * @param Retain $dialog
     * @return Operator|null
     */
    public function onRetain(Retain $dialog) : Operator;

    /**
     * 当 A Context 依赖 B Context 时, B Context 退出会导致这个流程.
     * 一层层地退出.
     *
     * @param Withdraw $dialog
     * @return Operator|null
     */
    public function onWithdraw(Withdraw $dialog) : ? Operator;


}