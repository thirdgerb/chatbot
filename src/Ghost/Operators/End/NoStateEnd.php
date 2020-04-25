<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\End;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;


/**
 * 无状态地结束当前的会话.
 * 不保存任何数据, 但不影响回复.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class NoStateEnd implements Operator
{
    public function invoke(Cloner $cloner): ? Operator
    {
        $cloner->noState();
        return null;
    }


}