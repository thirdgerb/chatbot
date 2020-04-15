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
use Commune\Ghost\Blueprint\Stage\Retrace;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Context $from
 */
class IRetraceStage extends AStage implements Retrace
{
    /**
     * @var Context
     */
    protected $from;

    public function __construct(
        Conversation $conversation,
        StageDef $stageDef,
        Context $self,
        Context $from
    )
    {
        $this->from = $from;
        parent::__construct($conversation, $stageDef, $self);
    }


}