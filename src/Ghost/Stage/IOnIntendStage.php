<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Stage;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Definition\StageDef;
use Commune\Blueprint\Ghost\Routing\Hearing;
use Commune\Blueprint\Ghost\Routing\Fallbacking;
use Commune\Blueprint\Ghost\Routing\Redirecting;
use Commune\Blueprint\Ghost\Runtime\Node;
use Commune\Blueprint\Ghost\Stage\OnIntend;
use Commune\Ghost\Routing\IHearing;
use Commune\Ghost\Routing\IFallbacking;
use Commune\Ghost\Routing\IRedirecting;

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
        Cloner $cloner,
        StageDef $stageDef,
        Node $self,
        Node $intending
    )
    {
        $this->intendingNode = $intending;
        parent::__construct($cloner, $stageDef, $self);
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

    public function redirect(): Redirecting
    {
        return new IRedirecting($this);
    }

    public function fallback(): Fallbacking
    {
        return new IFallbacking($this);
    }

    public function backward(): Hearing
    {
        return new IHearing();
    }


    public function __get($name)
    {
        if ($name === 'intending') {
            return $this->intendingContext
                ?? $this->intendingContext = $this->intendingNode->findContext($this->cloner);
        }

        return parent::__get($name);
    }

}