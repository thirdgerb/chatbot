<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Contexts;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Convo\ConvoInstance;
use Commune\Ghost\Prototype\Context\TContextStub;

/**
 * Context 的桩脚, 用于生成真正的 Context
 *
 * 可以用它 ContextStub::toInstance($convo) 来生成真正的 Context.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ContextStub implements Context
{
    use TContextStub;

    /**
     * @var string
     */
    protected $contextName;


    public function __construct(string $contextName, array $data)
    {
        $this->contextName = $contextName;
        $this->data = $data;
    }


    public function getName(): string
    {
        return $this->contextName;
    }

    /**
     * @param Conversation $session
     * @return Context
     */
    public function toInstance(Conversation $session): ConvoInstance
    {
        $data = empty($this->data) ? null : $this->data;
        return $session->newContext($this->contextName, $data);
    }


}