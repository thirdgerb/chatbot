<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Chat;

use Commune\Ghost\Blueprint\Cloner\ChatScope;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Babel\BabelSerializable;
use Commune\Support\Babel\TBabelSerializable;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IChatScope implements ChatScope, HasIdGenerator
{
    use ArrayAbleToJson, TBabelSerializable, IdGeneratorHelper;

    /**
     * @var string
     */
    protected $chatbotName;

    /**
     * @var string
     */
    protected $chatId;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var array
     */
    protected $shellChatIds;

    /**
     * @var bool
     */
    protected $changed = false;

    /**
     * IChatScope constructor.
     * @param string $chatbotName
     * @param string $chatId
     * @param string|null $sessionId
     * @param array $shellChatIds
     */
    public function __construct(
        string $chatbotName,
        string $chatId,
        ? string $sessionId,
        array $shellChatIds
    )
    {
        $this->chatbotName = $chatbotName;
        $this->chatId = $chatId;
        $this->sessionId = $sessionId ?? $this->createUuId();
        $this->shellChatIds = $shellChatIds;
        $this->changed = true;
    }

    public function setSessionId(string $sessionId): void
    {
        $this->changed = true;
        $this->sessionId = $sessionId;
    }


    public function toArray(): array
    {
        $fields = $this->__sleep();
        $data = [];
        foreach ($fields as $name) {
            $data[$name] = $this->{$name};
        }
        return $data;
    }

    public function isChanged(): bool
    {
        return $this->changed;
    }

    public static function createNewSerializable(array $input): ? BabelSerializable
    {
        $scope = new static(
            $input['chatbotName'],
            $input['chatId'],
            $input['sessionId'],
            $input['shellChatIds']
        );
        $scope->changed = false;
        return $scope;
    }

    public function resetSessionId(): void
    {
        $this->changed = true;
        $this->sessionId = $this->createUuId();
    }


    public function __sleep(): array
    {
        return [
            'chatbotName',
            'chatId',
            'sessionId',
            'shellChatIds',
        ];
    }

    public function addShellChat(string $chatId, string $shellName): void
    {
        $this->changed = true;
        $this->shellChatIds[$chatId] = $shellName;
    }

    public function removeShellChat(string $chatId): void
    {
        $this->changed = true;
        unset($this->shellChatIds[$chatId]);
    }


    public function __get($name)
    {
        return property_exists($this, $name) ? $this->{$name} : null;
    }
}