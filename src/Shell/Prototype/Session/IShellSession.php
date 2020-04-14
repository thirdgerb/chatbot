<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Session;

use Commune\Framework\Blueprint\Intercom\GhostInput;
use Commune\Framework\Blueprint\Server\Request;
use Commune\Framework\Blueprint\Server\Response;
use Commune\Framework\Blueprint\Server\Server;
use Commune\Framework\Blueprint\Session\SessionLogger;
use Commune\Framework\Blueprint\Session\SessionStorage;
use Commune\Framework\Contracts;
use Commune\Framework\Blueprint\Intercom\ShellInput;
use Commune\Framework\Blueprint\Intercom\ShellOutput;
use Commune\Framework\Contracts\Cache;
use Commune\Framework\Prototype\Session\ASession;
use Commune\Message\Blueprint\Message;
use Commune\Shell\Blueprint\Session\ShellSession;
use Commune\Shell\Blueprint\Session\ShellStorage;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Contracts\ShellRequest;
use Commune\Shell\Contracts\ShellResponse;
use Commune\Shell\ShellConfig;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read ShellConfig $shellConfig
 */
class IShellSession extends ASession implements ShellSession, HasIdGenerator
{
    use IdGeneratorHelper;

    const INJECTABLE_PROPERTIES = [
        'cache' => Contracts\Cache::class,
        'messenger' => Contracts\Messenger::class,
        'storage' => ShellStorage::class,
        'logger' => SessionLogger::class,
        'request' => ShellRequest::class,
        'response' => ShellResponse::class,
        'shell' => Shell::class,
        'shellInput' => ShellInput::class,
        'ghostInput' => GhostInput::class,
        'shellConfig' => ShellConfig::class,
        'server' => Server::class,
    ];

    const SESSION_ID_KEY = 'shell:%s:chat:%s:sid';

    /**
     * @var ShellOutput[]
     */
    protected $outputs = [];

    /**
     * @var string
     */
    protected $chatId;

    /**
     * @var ShellInput
     */
    protected $shellInput;

    /**
     * @var string
     */
    protected $sceneId;


    /**
     * @var string
     */
    protected $sessionId;

    /*------ i/o ------*/

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    /*------ properties ------*/

    public function getSessionId(): string
    {
        if (isset($this->sessionId)) {
            return $this->sessionId;
        }

        if ($this->isStateless()) {
            return $this->sessionId = $this->request->getSessionId()
                ?? $this->createUuId();
        }

        $id = $this->request->getSessionId();
        if (isset($id)) {
            return $this->sessionId = $id;
        }

        $key = $this->makeSessionIdKey();

        $id = $this->cache->get($key);
        if (empty($id)) {
            $id = $this->createUuId();
            $this->cache->set($key, $id, $this->getSessionExpire());
        } else {
            $this->cache->expire($key, $this->getSessionExpire());
        }

        return $this->sessionId = $id;
    }

    public function reset(): void
    {
        if (!$this->isStateless()) {
            $this->cache->forget($this->makeSessionIdKey());
        }
    }


    protected function makeSessionIdKey() : string
    {
        return printf(static::SESSION_ID_KEY, $this->shell->getShellName(), $this->getChatId());
    }


    /**
     * 生成 chatId
     * @return string
     */
    public function getChatId(): string
    {
        return $this->chatId
            ?? $this->chatId = $this->request->getChatId();
    }

    public function getStorage(): SessionStorage
    {
        return $this->storage;
    }

    public function getLogger(): SessionLogger
    {
        return $this->logger;
    }

    public function getSessionExpire(): int
    {
        return $this->shellConfig->sessionExpire;
    }

    public function getCache(): Cache
    {
        return $this->cache;
    }

    public function getSceneId(): string
    {
        if (isset($this->sceneId)) {
            return $this->sceneId;
        }

        $request = $this->request;
        $id = $request->getSceneId();
        $allow = $this->shellConfig->allowScene($id);
        if ($allow) {
            return $this->sceneId = $id;
        }

        $warning = $this->getApp()->getLogInfo()->shellNotAllowScene(
            $this->shell->getShellName(),
            $id
        );

        $this->getLogger()->warning($warning);

        return $this->sceneId = '';
    }


    public function output(Message $message): void
    {
        $this->outputs[] = $this->shellInput->output($message);
    }


    public function addShellOutputs(array $outputs): void
    {
        $this->outputs = array_merge($this->outputs, $outputs);
    }

    public function getShellOutputs(): array
    {
        return $this->outputs;
    }

    /*------ finish ------*/



    protected function flushInstances(): void
    {
        $this->outputs = [];
    }

    protected function saveSession(): void
    {
    }


}