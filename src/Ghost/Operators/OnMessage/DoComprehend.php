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

use Commune\Ghost\Blueprint\GhostConfig;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Operators\AbsOperator;
use Commune\Message\Abstracted\Comprehension;


/**
 * 对输入消息进行理解.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DoComprehend extends AbsOperator
{
    /**
     * @var StageDef
     */
    protected $stageDef;

    /**
     * @var GhostConfig
     */
    protected $ghostConfig;

    public function invoke(): ? Operator
    {
        $abs = $this->stageDef->comprehension();
        if (empty($abs)) {
            $abs = $this->ghostConfig->defaultComprehension;
        }

        /**
         * @var Comprehension $comprehension
         */
        $comprehension = $this->session->container->get($abs);
        $comprehension->comprehend($this->session->incoming);

        // return StageRouting
    }


}