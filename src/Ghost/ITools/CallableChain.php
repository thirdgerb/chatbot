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

use Commune\Blueprint\Ghost\Tools\Invoker;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CallableChain
{

    /**
     * @var Invoker
     */
    protected $invoker;

    /**
     * @var callable[]
     */
    protected $callers = [];

    /**
     * CallableChain constructor.
     * @param Invoker $invoker
     * @param callable[] $callers
     */
    public function __construct(Invoker $invoker, array $callers)
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