<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\OnMessage;

use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Operators\AbsOperator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Intending extends AbsOperator
{
    /**
     * @var StageDef
     */
    protected $fromStage;

    /**
     * @var StageDef
     */
    protected $intendingStage;

    public function invoke(): ? Operator
    {
        // 生成 IntendDialog, 并执行.
        // return
    }


}