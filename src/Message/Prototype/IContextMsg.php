<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype;

use Commune\Message\Blueprint\ContextMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IContextMsg extends AMessage implements ContextMsg
{
    /**
     * @var string
     */
    protected $contextId;

    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var array
     */
    protected $entities;

    /**
     * IContextMsg constructor.
     * @param string $contextId
     * @param string $contextName
     * @param array $entities
     */
    public function __construct(string $contextId, string $contextName, array $entities)
    {
        $this->contextId = $contextId;
        $this->contextName = $contextName;
        $this->entities = $entities;
        parent::__construct();
    }


    public function getContextId(): string
    {
        return $this->contextId;
    }

    public function getContextName(): string
    {
        return $this->contextName;
    }

    public function getEntities(): array
    {
        return $this->entities;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function __sleep(): array
    {
        return [
            'contextId',
            'contextName',
            'entities',
            'createdAt',
        ];
    }


}