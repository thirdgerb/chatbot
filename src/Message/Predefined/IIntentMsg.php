<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Predefined;

use Commune\Message\Blueprint\IntentMsg;
use Commune\Message\Blueprint\Tag\MsgLevel;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IIntentMsg extends AMessage implements IntentMsg
{

    /**
     * @var array
     */
    protected $entities;

    /**
     * @var string
     */
    protected $intentName;

    /**
     * @var string
     */
    protected $level;

    /**
     * IIntentMsg constructor.
     * @param string $intentName
     * @param array $entities
     * @param string $level
     * @param float|null $createdAt
     */
    public function __construct(
        string $intentName = '',
        array $entities = [],
        string $level = MsgLevel::INFO,
        float $createdAt = null
    )
    {
        $this->intentName = StringUtils::namespaceSlashToDot($intentName);
        $this->entities = $entities;
        $this->level = $level;
        parent::__construct($createdAt);
    }

    public function __sleep(): array
    {
        return [
            'intentName',
            'entities',
            'level',
            'createdAt',
        ];
    }

    public function getIntentName(): string
    {
        return $this->intentName;
    }

    public function getEntities(): array
    {
        return $this->entities;
    }

    public function isEmpty(): bool
    {
        return $this->intentName === '';
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function __get($name)
    {
        return $this->entities[$name] ?? null;
    }

}