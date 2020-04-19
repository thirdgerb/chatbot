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

use Commune\Framework\Blueprint\Session;
use Commune\Framework\Contracts\Cache;
use Commune\Ghost\Blueprint\Cloner\Cloner;
use Commune\Ghost\Blueprint\Cloner\ChatScope;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ICloner implements Cloner, HasIdGenerator, Spied
{
    use IdGeneratorHelper, SpyTrait;

    const CHAT_LOCKER_KEY = 'ghost:chat:%s:locker';

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var bool
     */
    protected $stateless;

    /**
     * @var ChatScope
     */
    protected $scope;

    /**
     * @var Cache
     */
    protected $cache;

    public function __construct(Conversation $session)
    {
        $this->uuid = $session->getUuId();
        $this->stateless = $session->isStateless();
        static::addRunningTrace($this->uuid, $this->uuid);

        if ($session->isStateless()) {
            $this->initNewScope($session);
        } else {
            $this->cache = $session->cache;
            $this->restoreScope($session);
        }
    }

    protected function initNewScope(Conversation $session) : void
    {
        $shellChatId = $session->ghostInput->chatId;
        $ghostInput = $session->ghostInput;
        $scope = new IChatScope(
            $ghostInput->getChatbotName(),
            $shellChatId,
            $ghostInput->getSessionId() ?? $this->createUuId(),
            [
                $shellChatId => $ghostInput->shellName
            ]
        );
        $this->scope = $scope;
    }

    protected function restoreScope(Conversation $session) : void
    {
        $ghostInput = $session->ghostInput;
        $shellChatId = $ghostInput->chatId;
        $scope = $session->driver->findScope($shellChatId);

        if (isset($scope)) {
            $sessionId = $ghostInput->getSessionId();

            // 一个时间只有一个 sessionId 是允许的.
            if (isset($sessionId)) {
                $scope->setSessionId($sessionId);
            }

            $this->scope = $scope;
        } else {
            $this->initNewScope($session);
        }
    }

    public function getCloneId(): string
    {
        return $this->scope->chatId;
    }

    public function getSessionId(): string
    {
        return $this->scope->sessionId;
    }

    public function getScope(): ChatScope
    {
        return $this->scope;
    }

    public function resetSession(): void
    {
        $this->scope->resetSessionId();
    }

    public function lock(): bool
    {
        if ($this->stateless) {
            return true;
        }
        $key = $this->makeChatLockerKey();
        return $this->cache->lock($key);
    }

    public function unlock(): bool
    {
        if ($this->stateless) {
            return true;
        }
        $key = $this->makeChatLockerKey();
        return $this->cache->unlock($key);
    }

    protected function makeChatLockerKey() : string
    {
        return printf(static::CHAT_LOCKER_KEY, $this->getCloneId());
    }

    public function setScope(ChatScope $scope): void
    {
        $this->scope = $scope;
    }

    public function save(Session $session): void
    {
        if ($this->stateless) {
            return;
        }
        // todo
    }

    public function __destruct()
    {
        $this->scope = null;
        $this->cache = null;
        static::removeRunningTrace($this->uuid);
    }

}