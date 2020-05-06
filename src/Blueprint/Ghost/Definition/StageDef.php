<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Definition;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialogue\Receive;
use Commune\Blueprint\Ghost\Dialogue\Withdraw;
use Commune\Blueprint\Ghost\Dialogue\Fulfill;
use Commune\Blueprint\Ghost\Dialogue\Activate;


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
    public function getStageName() : string;

    /**
     * stage 的全名, 通常对应 IntentName
     * @return string
     */
    public function getFullStageName() : string;

    /**
     * 所属 Context 的名称.
     * @return string
     */
    public function getContextName() : string;

    /**
     * @return IntentDef
     */
    public function asIntentDef() : IntentDef;

    /**
     * @param Cloner $cloner
     * @return array|null
     */
    public function comprehendPipes(Cloner $cloner) : ? array;

    /*------- intend to stage -------*/

    /**
     * 激活当前的 Stage, 然后等待回调.
     * @param Activate $dialog
     * @return Dialog
     */
    public function onActivate(Activate $dialog) : Dialog;

    /**
     * 接收到一个用户消息时.
     *
     * @param Receive $dialog
     * @return Dialog|null
     */
    public function onReceive(Receive $dialog) : Dialog;

    /**
     * 依赖一个 Context, 而目标 Context 语境完成后回调.
     * @param Fulfill $dialog
     * @param Context $fulfilled
     * @return Dialog
     */
    public function onFulfill(Fulfill $dialog, Context $fulfilled) : Dialog;

    /**
     * 当 A Context 依赖 B Context 时, B Context 退出会导致这个流程.
     * 一层层地退出.
     *
     * @param Withdraw $dialog
     * @return Dialog|null
     */
    public function onWithdraw(Withdraw $dialog) : ? Dialog;


}