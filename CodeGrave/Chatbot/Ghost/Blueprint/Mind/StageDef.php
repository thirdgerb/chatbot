<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint\Mind;

use Commune\Chatbot\Ghost\Blueprint\Callables\Action;
use Commune\Chatbot\Ghost\Blueprint\Dialog\OnIntending;
use Commune\Chatbot\Ghost\Blueprint\Meta\Wrapper;
use Commune\Chatbot\Ghost\Blueprint\Redirector;


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


    /**
     * 指定的 NLUService. 不存在则使用默认的.
     * @return null|string
     */
    public function nluService() : ? string;

    /*------- pre-stage event -------*/

    /**
     * 作为意图被命中时, 还未进入当前 Stage
     *
     * @param OnIntending $dialog
     * @return Redirector
     */
    public function onIntending(
        OnIntending $dialog
    ) : Redirector;


    /*------- stage event -------*/

    /**
     * 依赖其它语境, 接受该语境回调时
     * @return Redirector
     */
    public function onFallback(
    ) : Redirector;


    /**
     */
    public function onWoke(
    ) : Redirector ;

    /**
     * 在同一个 Context 内部, stage 切换的时候.
     * @return Redirector
     */
    public function onStaging(
    ) : Redirector;

    /**
     * 正式进入 Stage 后
     * @return Redirector
     */
    public function onStarting(
    ) : Redirector;

    /**
     * 没有命中任何分支, 由当前 Stage 自行响应.
     */
    public function onHeard(
    ) : Redirector;

    public function onCancel(
    ) : Redirector ;


    public function onQuit(
    ) : Redirector ;

    public function onFailure(
    ) : Redirector ;

}