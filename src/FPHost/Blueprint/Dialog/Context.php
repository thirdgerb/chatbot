<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\FPHost\Blueprint\Dialog;

use Commune\FPHost\Blueprint\Memory\Recollection;


/**
 * 一个语境的数据. 同时是一个记忆单元.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Context extends Recollection
{
    public function contextName() : string;

}