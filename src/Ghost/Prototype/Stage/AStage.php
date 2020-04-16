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
use Commune\Ghost\Blueprint\Stage\Stage;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AStage implements Stage, Spied
{
    use SpyTrait;

    protected $uuid;

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * @var StageDef
     */
    protected $stageDef;

    /**
     * @var Node
     */
    protected $self;


    /*------- cached -------*/

    /**
     * @var Context
     */
    protected $selfContext;

    /**
     * AStage constructor.
     * @param Conversation $conversation
     * @param StageDef $stageDef
     * @param Node $self
     */
    public function __construct(
        Conversation $conversation,
        StageDef $stageDef,
        Node $self
    )
    {
        $this->conversation = $conversation;
        $this->uuid = md5($self->contextId . $stageDef->getFullname() . static::class);
        $this->stageDef = $stageDef;
        $this->self = $self;
        static::addRunningTrace($this->uuid, $this->uuid);
    }


    /**
     * @param $name
     * @return null
     *
     * * @property-read Conversation $conversation
     * @property-read StageDef $def
     * @property-read Context $self
     */
    public function __get($name)
    {
        switch ($name) {
            case 'conversation' :
                return $this->conversation;
            case 'def' :
                return $this->def;
            case 'self' :
                return $this->selfContext
                    ?? $this->selfContext = $this->self->findContext($this->conversation);
            default :
                return null;
        }
    }


    public function __destruct()
    {
        static::removeRunningTrace($this->uuid);
    }

}