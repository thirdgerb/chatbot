<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\ITools;

use Commune\Blueprint\Ghost\Tools\DialogContainer;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CallableChain
{

    /**
     * @var DialogContainer
     */
    protected $invoker;

    /**
     * @var callable[]
     */
    protected $callers = [];

    /**
     * CallableChain constructor.
     * @param DialogContainer $invoker
     * @param callable[] $callers
     */
    public function __construct(DialogContainer $invoker, array $callers)
    {
        $this->invoker = $invoker;
        $this->callers = $callers;
    }

    public function __invoke()
    {
        while($caller = array_shift($caller)) {
            $this->invoker->call($caller);
        }
    }

}