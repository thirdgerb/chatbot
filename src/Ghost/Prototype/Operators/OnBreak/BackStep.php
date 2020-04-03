<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\OnBreak;

use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Prototype\Operators\AbsOperator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class BackStep extends AbsOperator
{
    /**
     * @var int
     */
    protected $steps;

    public function invoke(): ? Operator
    {
        // 执行 backSteps
        return null;
    }


}