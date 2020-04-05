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
use Commune\Framework\Blueprint\Session\SessionLogger;
use Commune\Framework\Blueprint\Session\SessionStorage;
use Commune\Framework\Contracts;
use Commune\Framework\Blueprint\Intercom\ShellInput;
use Commune\Framework\Blueprint\Intercom\ShellOutput;
use Commune\Framework\Contracts\Cache;
use Commune\Framework\Prototype\Session\ASession;
use Commune\Message\Blueprint\Message;
use Commune\Shell\Blueprint\Session\ShlSession;
use Commune\Shell\Blueprint\Session\ShlSessionStorage;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Contracts\ShlRequest;
use Commune\Shell\Contracts\ShlResponse;
use Commune\Shell\ShellConfig;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read ShellConfig $shellConfig
 */
class IShlSession extends ASession implements ShlSession, HasIdGenerator
{
    use IdGeneratorHelper;

    const INJECTABLE_PROPERTIES = [
        'cache' => Contracts\Cache::class,
        'messenger' => Contracts\Messenger::class,
        'storage' => ShlSessionStorage::class,
        'logger' => SessionLogger::class,
        'request' => ShlRequest::class,
        'response' => ShlResponse::class,
        'shell' => Shell::class,
        'shellInput' => ShellInput::class,
        'ghostInput' => GhostInput::class,
        'shellConfig' => ShellConfig::class,
    ];

    const SESSION_ID_KEY = 'shell:%s:chat:%s:sessionId';

    /**
     * @var ShellOutput[]
     */
    protected $outputs = [];

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var string
     */
    protected $chatId;

    /**
     * @var ShellInput
     */
    protected $shellInput;

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

    protected function getSessionIdKey(): string
    {
        return printf(
            static::SESSION_ID_KEY,
            $this->shell->getShellName(),
            $this->getChatId()
        );
    }

    public function getSessionExpire(): int
    {
        return $this->shellConfig->sessionExpire;
    }

    public function getCache(): Cache
    {
        return $this->cache;
    }

    public function reset(): void
    {
        if ($this->isStateless()) {
            return;
        }

        $key = $this->getSessionIdKey();
        $this->cache->forget($key);
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