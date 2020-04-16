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
use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Ghost\Blueprint\Stage\Intend;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IIntendStage extends AStage implements Intend
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

    public function __get($name)
    {
        if ($name === 'intending') {
            return $this->intendingContext
                ?? $this->intendingContext = $this->intendingNode->findContext($this->conversation);
        }

        return parent::__get($name);
    }

}