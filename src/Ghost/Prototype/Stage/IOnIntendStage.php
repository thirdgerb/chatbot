<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Stage;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Routing\Backward;
use Commune\Ghost\Blueprint\Routing\Fallback;
use Commune\Ghost\Blueprint\Routing\Redirect;
use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Ghost\Blueprint\Stage\OnIntend;
use Commune\Ghost\Prototype\Routing\IBackward;
use Commune\Ghost\Prototype\Routing\IFallback;
use Commune\Ghost\Prototype\Routing\IRedirect;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IOnIntendStage extends AStage implements OnIntend
{
    /**
     * @var Context
     */
    protected $intendingContext;

    /**
     * @var Node
     */
    protected $intendingNode;

    public function __construct(
        Conversation $conversation,
        StageDef $stageDef,
        Node $self,
        Node $intending
    )
    {
        $this->intendingNode = $intending;
        parent::__construct($conversation, $stageDef, $self);
    }

    public function call($caller, array $parameters = [])
    {
        $intending = $this->intending;

        $parameters['intending'] = $intending;
        foreach($intending->getInterfaces() as $interface) {
            $parameters[$interface] = $intending;
        }

        return parent::call($caller, $parameters);
    }

    public function redirect(): Redirect
    {
        return new IRedirect($this);
    }

    public function fallback(): Fallback
    {
        return new IFallback($this);
    }

    public function backward(): Backward
    {
        return new IBackward();
    }


    public function __get($name)
    {
        if ($name === 'intending') {
            return $this->intendingContext
                ?? $this->intendingContext = $this->intendingNode->findContext($this->conversation);
        }

        return parent::__get($name);
    }

}