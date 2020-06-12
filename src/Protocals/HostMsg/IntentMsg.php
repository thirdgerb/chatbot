<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\HostMsg;

use Commune\Protocals\HostMsg;
use Commune\Contracts\Trans\Translatable;

/**
 * Ghost 对外发表的响应意图.
 * 通常会被 Renderer 解析成多个其它类型的 HostMsg
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface IntentMsg extends HostMsg, Translatable
{
   /*------ struct 特殊字段名常量 ------*/

    const INTENT_NAME_FIELD = 'intentName';
    const LEVEL_FIELD = 'level';


    public function getIntentName() : string;

    /**
     * 除去 intentName, level 之外的所有参数.
     * @return array
     */
    public function getEntities() : array;

}