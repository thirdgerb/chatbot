<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint;

use Commune\Chatbot\Ghost\Blueprint\Context\Scope;
use Commune\Chatbot\Ghost\Blueprint\Memory\Recollection;

/**
 * 一个语境的数据. 同时是一个记忆单元.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Context extends Recollection
{

    /*------- status -------*/

    /**
     * 当前 Context 的唯一ID
     * @return string
     */
    public function contextId() : string;

    /**
     * 当前 Context 的名称
     * @return string
     */
    public function contextName() : string;

    /**
     * 当缉拿 Context 所处的 Stage
     * @return string
     */
    public function stageName() : string;

    /**
     * 当前所处的 Scope
     * @return Scope
     */
    public function scope() : Scope;

}