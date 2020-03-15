<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\FPHost\Blueprint\Mind;

use Commune\FPHost\Blueprint\Meta\Wrapper;
use Commune\FPHost\Blueprint\Redirector;


/**
 * Stage 的封装对象
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface StageDef extends Wrapper
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

    /*------- life circle -------*/

    /**
     * 进入 Stage 之前
     * @return Redirector
     */
    public function onRedirect() : Redirector;

    /**
     * 正式进入 Stage 后
     * @return Redirector
     */
    public function onStart() : Redirector;

    /**
     * 没有命中任何分支,
     * @return Redirector
     */
    public function onHeard() : Redirector;

    /**
     * 作为意图或选项被命中时.
     * @return Redirector
     */
    public function onIntended() : Redirector;


    /**
     * 在 sleep 状态中, 被唤醒时
     * @return Redirector
     */
    public function onWake() : Redirector ;


    /**
     * 遇到退出逻辑时
     * @return Redirector
     */
    public function onExit() : Redirector ;

}