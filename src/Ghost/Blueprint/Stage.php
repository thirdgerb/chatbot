<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint;

use Commune\Ghost\Blueprint\Mind\StageDef;


/**
 * Stage 状态机. 用来决定 Stage 的行为.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Stage
{

    // stage 当前的状态
    public function state() : string;


    public function stageDef() : StageDef;
}