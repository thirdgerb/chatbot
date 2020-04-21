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

use Commune\Blueprint\Ghost\Convo\Conversation;

/**
 * 一个可以被命中的意图的定义. 通常从属于 StageDef.
 * 与 NLU 定义的意图不同, 这里的 IntentDef 可用于各种内部判断逻辑.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface RoutingDef
{

    /**
     * 是否可以全局匹配到.
     * @return bool
     */
    public function isPublic() : bool;

    /**
     * 从属的 Stage
     * @return StageDef
     */
    public function getStageDef() : StageDef;

    /*--------- 操作 ---------*/

    public function validate(Conversation $session) : bool;
}