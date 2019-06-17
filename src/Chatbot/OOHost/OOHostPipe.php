<?php


namespace Commune\Chatbot\OOHost;


use Closure;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\Framework\Pipeline\ChatbotPipeImpl;
use Commune\Chatbot\Framework\Utils\OnionPipeline;
use Commune\Chatbot\OOHost\Session\Driver as SessionDriver;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionImpl;
use Commune\Chatbot\OOHost\Session\Snapshot;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * 面向对象的 host 机器人提供的chatbot pipe
 */
class OOHostPipe extends ChatbotPipeImpl implements HasIdGenerator
{
    use IdGeneratorHelper;


    /**
     * @var CacheAdapter
     */
    public $cache;

    /**
     * @var SessionDriver
     */
    public $driver;

    /**
     * @var OOHostConfig
     */
    public $hostConfig;

    public $chatbotConfig;

    public function __construct(
        CacheAdapter $cache,
        SessionDriver $driver,
        ChatbotConfig $config
    )
    {
        $this->cache = $cache;
        $this->driver = $driver;
        $this->chatbotConfig = $config;
        $this->hostConfig = $config->host;
    }


    public function handleUserMessage(Conversation $conversation, Closure $next): Conversation
    {
        // 用 sessionId 来唤醒一个session.
        $chatId = $conversation->getChat()->getChatId();

        $session = $this->makeSession($chatId, $conversation);

        $session = $this->callSession($session);

        $session->finish();

        if ($session->isQuiting()) {
            $conversation
                ->onFinish(function(
                    ChatServer $server,
                    Conversation $conversation
                ) {
                    $server->closeClient($conversation);
                });
        }


        // 既然当前 session 已经搞定, 就不往后走了.
        if ($session->isHeard()) {
            return $conversation;
        }


        return $next($conversation);
    }

    public function callSession(Session $session) : Session
    {
        if (empty($this->hostConfig->sessionPipes)) {
            return $this->doCallSession($session);

        // 还是要走管道.
        } else {
            $pipeline = new OnionPipeline(
                $session->conversation,
                $this->hostConfig->sessionPipes
            );

            return $pipeline
                ->via('handle')
                ->send(
                    $session,
                    $this->getDestination()
                );
        }
    }

    public function doCallSession(Session $session) : Session
    {
        $session->hear($session->incomingMessage->message);
        return $session;
    }

    
    public function getDestination() : Closure
    {
        return function(Session $session) {
            return $this->doCallSession($session);
        };
    }

    public function getSessionId(string $key) : string
    {
        return $this->cache->get($key) ?? $this->createUuId();
    }

    public function makeSession(
        string $belongsTo,
        Conversation $conversation
    ) : Session
    {
        return new SessionImpl(
            $belongsTo,
            $this->cache,
            $conversation,
            $this->driver
        );
    }

    public function fetchSnapshot(string $key) : Snapshot
    {
        if ($this->cache->has($key)) {
            $unserialized = unserialize($this->cache->get($key));

            if ($unserialized instanceof Snapshot) {
                return $unserialized;
            }

            $this->cache->forget($key);
        }

        $sessionId = $this->createUuId();
        return new Snapshot($sessionId);
    }

    public function onUserMessageFinally(Conversation $conversation): void
    {
    }


}