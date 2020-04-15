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
     * @var Context
     */
    protected $self;

    /**
     * AStage constructor.
     * @param Conversation $conversation
     * @param StageDef $stageDef
     * @param Context $self
     */
    public function __construct(
        Conversation $conversation,
        StageDef $stageDef,
        Context $self
    )
    {
        $this->conversation = $conversation;
        $this->uuid = $conversation->getUuid();
        $this->stageDef = $stageDef;
        $this->self = $self;
        static::addRunningTrace($this->uuid, $this->uuid);
    }


    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        return null;
    }


    public function __destruct()
    {
        static::removeRunningTrace($this->uuid);
        $this->stageDef = null;
        $this->conversation = null;
    }

}