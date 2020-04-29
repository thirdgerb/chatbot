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
use Commune\Blueprint\Ghost\Runtime\Node;
use Commune\Blueprint\Ghost\Stage\OnRetrace;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Cloner $cloner
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
        Cloner $cloner,
        StageDef $stageDef,
        Node $self,
        Node $from
    )
    {
        $this->fromNode = $from;
        parent::__construct($cloner, $stageDef, $self);
    }

    public function __get($name)
    {
        if ($name === 'from') {
            return $this->fromContext
                ?? $this->fromContext = $this->fromNode->findContext($this->cloner);
        }

        return parent::__get($name);
    }

}