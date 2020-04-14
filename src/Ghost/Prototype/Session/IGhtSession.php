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
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Convo\GhostSessionScope;
use Commune\Ghost\Blueprint\Convo\Scene;
use Commune\Ghost\Blueprint\Speak\Speaker;
use Commune\Ghost\Contracts\GhostRequest;
use Commune\Ghost\Contracts\GhostResponse;
use Commune\Ghost\Contracts\Driver;
use Commune\Ghost\GhostConfig;
use Commune\Message\Blueprint\Message;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read GhostConfig $ghostConfig
 */
class IConversation extends ASession implements Conversation
{
    const SESSION_ID_KEY = 'ghost:chatId:%s:sessionId';

    const INJECTABLE_PROPERTIES = [
        'ghostInput' => GhostInput::class,
        'scene' => Scene::class,
        'scope' => GhostSessionScope::class,
        'logger' => SessionLogger::class,
        'ghost' => Ghost::class,
        'mind' => Mindset::class,
        'metaReg' => MetaRegistrar::class,
        'driver' => Driver::class,
        'cache' => Cache::class,
        'messenger' => Messenger::class,
        'auth' => Authority::class,
        'memory' => Memory::class,
        'speaker' => Speaker::class,
        'request' => GhostRequest::class,
        'response' => GhostResponse::class,
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
        return $this->cloner->getCloneId();
    }

    public function getSessionId(): string
    {
        return $this->cloner->getSessionId();
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

    public function deliver(string $cloneId, Message $message): void
    {
        $scope = $this->driver->findScope($cloneId);

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
                    $cloneId => $this->ghostInput->shellName
                ]
            );
        }

        $this->delivery[$cloneId][] = $message;
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
                $this->cloner->getScope()->shellChatIds
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
        $this->cloner->save($this);
        $this->runtime->save($this);
        $this->memory->save($this);

    }


}