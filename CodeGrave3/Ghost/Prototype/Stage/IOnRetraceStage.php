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
use Commune\Ghost\Blueprint\Stage\OnRetrace;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Conversation $conversation
 * @property-read StageDef $def
 * @property-read Context $self
 * @property-read Context $from
 */
class IOnRetraceStage extends AStage implements OnRetrace
{
    /**
     * @var Node
     */
    protected $fromNode;

    /**
     * @var Context
     */
    protected $fromContext;


    public function __construct(
        Conversation $conversation,
        StageDef $stageDef,
        Node $self,
        Node $from
    )
    {
        $this->fromNode = $from;
        parent::__construct($conversation, $stageDef, $self);
    }

    public function __get($name)
    {
        if ($name === 'from') {
            return $this->fromContext
                ?? $this->fromContext = $this->fromNode->findContext($this->conversation);
        }

        return parent::__get($name);
    }

}