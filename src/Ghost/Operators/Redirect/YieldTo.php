<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Redirect;

use Commune\Blueprint\Ghost\Context\Context;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class YieldTo implements Operator
{
    public function __construct(
        string $shellName,
        string $shellId,
        Context $asyncContext,
        Context $toContext = null,
        string $wakeThreadId = null,
        int $expire = null
    )
    {


    }

    public function invoke(Cloner $cloner): ? Operator
    {

    }


}