<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Context;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Convo\ConvoInstance;
use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Ghost\Exceptions\ConvoInstanceException;
use Commune\Ghost\Prototype\Runtime\INode;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\DI\TInjectable;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AbsContext implements Context, Spied
{
    use ArrayAbleToJson, TInjectable, SpyTrait;

    /**
     * @var Conversation|null
     */
    protected $_conversation;

    /**
     * @var string
     */
    protected $_id;

    /**
     * @var array
     */
    protected $_data;

    /**
     * @var int
     */
    protected $_priority;

    /**
     * AbsContext constructor.
     * @param array $_data
     */
    public function __construct(array $_data)
    {
        $this->_data = $_data;
    }


    public function getId(): string
    {
        if (!$this->isInstanced()) {
            throw new ConvoInstanceException(static::class);
        }
        return $this->_id;
    }

    public function toNewNode(): Node
    {
        return new INode(

        );

    }


    public function isInstanced(): bool
    {
        return isset($this->_conversation);
    }

    public function toInstance(Conversation $conversation): ConvoInstance
    {
        $this->_conversation = $conversation;

        $def = $conversation->mind->contextReg()->getDef($this->getName());
        $this->_id = $def->makeId($conversation->scope);
        $this->_priority = $def->getPriority();

        static::addRunningTrace($this->_id, $this->_id);

        return $this;
    }

    public function getInterfaces(): array
    {
        return static::getInterfacesOf(Context::class);
    }


    public function __destruct()
    {
        if (isset($this->_id)) {
            static::removeRunningTrace($this->_id);
        }
    }
}