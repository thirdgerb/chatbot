<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\OperatorsBack\Redirect;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Prototype\OperatorsBack\AbsOperator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SleepTo extends AbsOperator
{
    /**
     * @var Context|null
     */
    protected $to;

    /**
     * @var int
     */
    protected $gc;

    /**
     * SleepTo constructor.
     * @param Context|null $to
     * @param int $gc
     */
    public function __construct(?Context $to, int $gc = 0)
    {
        $this->to = $to;
        $this->gc = $gc;
    }


}