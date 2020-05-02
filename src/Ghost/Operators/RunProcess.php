<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Runtime\Trace;
use Commune\Ghost\Operators\Flows\ConfuseFlow;
use Commune\Ghost\Operators\Flows\InputFlow;

/**
 * 启动多轮对话进程.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RunProcess implements Operator
{
    protected $trace;

    public function __construct(Trace $trace)
    {
        $this->trace = $trace;
    }





}