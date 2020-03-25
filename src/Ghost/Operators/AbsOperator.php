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

use Commune\Ghost\Blueprint\Definition\Mindset;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Runtime;
use Commune\Ghost\Blueprint\Session\GhtSession;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AbsOperator implements Operator
{
    /**
     * @var GhtSession
     */
    protected $session;

    /**
     * @var Runtime;
     */
    protected $runtime;

    /**
     * @var Mindset
     */
    protected $mind;

    /**
     * AbsOperator constructor.
     * @param GhtSession $session
     */
    public function __construct(GhtSession $session)
    {
        $this->session = $session;
    }


}