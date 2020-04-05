<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Session;

use Commune\Framework\Blueprint\Intercom;
use Commune\Framework\Blueprint\Intercom\GhostInput;
use Commune\Framework\Blueprint\Server\Request;
use Commune\Framework\Blueprint\Server\Response;
use Commune\Framework\Blueprint\Server\Server;
use Commune\Framework\Blueprint\Session\SessionLogger;
use Commune\Framework\Blueprint\Session\SessionStorage;
use Commune\Framework\Contracts\Cache;
use Commune\Framework\Contracts\Messenger;
use Commune\Framework\Prototype\Session\ASession;
use Commune\Ghost\Blueprint\Auth\Authority;
use Commune\Ghost\Blueprint\Ghost;
use Commune\Ghost\Blueprint\Memory\Memory;
use Commune\Ghost\Blueprint\Meta\MetaRegistrar;
use Commune\Ghost\Blueprint\Mind\Mindset;
use Commune\Ghost\Blueprint\Session\GhtSession;
use Commune\Ghost\Blueprint\Session\GhtSessionScope;
use Commune\Ghost\Blueprint\Session\Scene;
use Commune\Ghost\Blueprint\Speak\Speaker;
use Commune\Ghost\Contracts\GhtRequest;
use Commune\Ghost\Contracts\GhtResponse;
use Commune\Ghost\Contracts\SessionDriver;
use Commune\Ghost\GhostConfig;
use Commune\Message\Blueprint\Message;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read GhostConfig $ghostConfig
 */
class IGhtSession extends ASession implements GhtSession
{
    const SESSION_ID_KEY = 'ghost:chatId:%s:sessionId';

    const INJECTABLE_PROPERTIES = [
        'ghostInput' => GhostInput::class,
        'scene' => Scene::class,
        'scope' => GhtSessionScope::class,
        'logger' => SessionLogger::class,
        'ghost' => Ghost::class,
        'mind' => Mindset::class,
        'metaReg' => MetaRegistrar::class,
        'driver' => SessionDriver::class,
        'cache' => Cache::class,
        'messenger' => Messenger::class,
        'auth' => Authority::class,
        'memory' => Memory::class,
        'speaker' => Speaker::class,
        'request' => GhtRequest::class,
        'response' => GhtResponse::class,
        'server' => Server::class,
        'ghostConfig' => GhostConfig::class,
        'storage' => SessionStorage::class,
    ];

    /**
     * @var Intercom\GhostOutput[]
     */
    protected $outputs = [];

    /**
     * @var Message[]
     */
    protected $delivery = [];
//    /**
//     * @var string
//     */
//    protected $sessionId;
//
//    public function getSessionId(): string
//    {
//        if (isset($this->sessionId)) {
//            return $this->sessionId;
//        }
//
//        if ($this->isStateless()) {
//            return $this->sessionId
//                ?? $this->ghostInput->getSessionId()
//                ?? $this->sessionId = $this->createUuId();
//        }
//
//        $sessionId = $this->ghostInput->getSessionId();
//
//
//        $key = $this->getSessionIdKey();
//        $cache = $this->getCache();
//        $expire = $this->getSessionExpire();
//        $expire = $expire > 0 ? $expire : null;
//
//        $id = $cache->get($key);
//
//        if (empty($id)) {
//            $id = $this->createUuId();
//            $cache->set($key, $id, $expire);
//        }
//        $cache->expire($key, $expire);
//        return $this->sessionId = $id;
//    }
//
//    protected function getSessionIdKey(): string
//    {
//        return printf(
//            static::SESSION_ID_KEY,
//            $this->getChatId()
//        );
//    }
//
//    public function reset(): void
//    {
//        if ($this->isStateless()) {
//            return;
//        }
//
//        $key = $this->getSessionIdKey();
//        $this->cache->forget($key);
//    }

    /*------ cached ------*/

    protected $sceneId;

    /*------ i/o ------*/

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * 生成 chatId
     * @return string
     */
    public function getChatId(): string
    {
        return $this->chat->getChatId();
    }

    public function getSessionId(): string
    {
        return $this->chat->getSessionId();
    }

    public function getSceneId(): string
    {
        return $this->sceneId
            ?? $this->ghostInput->getSceneId();
    }

    public function getSessionExpire(): int
    {
        return $this->ghostConfig->sessionExpire;
    }


    /*-------- components --------*/

    public function getStorage(): SessionStorage
    {
        return $this->storage;
    }

    public function getLogger(): SessionLogger
    {
        return $this->logger;
    }

    public function getCache(): Cache
    {
        return $this->cache;
    }

    /*---------- output ---------*/

    public function deliver(string $chatId, Message $message): void
    {
        $scope = $this->driver->findScope($chatId);

        // 如果能找到 scope, 就向目标进行投递
        if (isset($scope)) {
            $outputs = $this->ghostInput->derive(
                $message,
                $scope->shellChatIds
            );
        } else {
            $outputs = $this->ghostInput->derive(
                $message,
                [
                    $chatId => $this->ghostInput->shellName
                ]
            );
        }

        $this->delivery[$chatId][] = $message;
        $this->outputs = array_merge($this->outputs, $outputs);
    }

    public function getOutputs(): array
    {
        return $this->outputs;
    }

    public function output(Message $message): void
    {
        $outputs = $this->ghostInput
            ->derive(
                $message,
                $this->chat->getScope()->shellChatIds
            );

        $this->delivery[$this->getChatId()][] = $message;
        $this->outputs = array_merge(
            $this->outputs,
            $outputs
        );
    }

    public function getDelivery(): array
    {
        return $this->delivery;
    }


    /*-------- finish --------*/

    protected function flushInstances(): void
    {
        $this->outputs = [];
        $this->delivery = [];
    }

    protected function saveSession(): void
    {
        $this->chat->save($this);
        $this->runtime->save($this);
        $this->memory->save($this);

    }


}