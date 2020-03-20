<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost;

use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Config\Children\OOHostConfig;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\Framework\Constants\CacheKey;
use Commune\Chatbot\Framework\Pipeline\ChatbotPipeImpl;
use Commune\Chatbot\Framework\Utils\OnionPipeline;
use Commune\Chatbot\Ghost\Blueprint\Ghost;
use Commune\Chatbot\Ghost\Blueprint\Session;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * 运行 FPHost 的管道
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OnUserMessagePipe extends ChatbotPipeImpl implements HasIdGenerator
{
    use IdGeneratorHelper;

    /**
     * todo 修改为 FPHostConfig
     * @var OOHostConfig
     */
    public $hostConfig;

    /**
     * @var ChatbotConfig
     */
    public $chatbotConfig;

    /**
     * @var CacheAdapter
     */
    public $cache;

    public function handleUserMessage(Conversation $conversation, \Closure $next): Conversation
    {
        $chatId = $conversation->getChat()->getChatId();

        // 绑定 SessionId
        $sessionId = $this->fetchSessionId($conversation, $chatId);
        $conversation->share(Session\SessionId::class, $sessionId);

        // 生成 session
        $session = $this->makeSession($conversation);

        // 使用管道运行 Session
        $session = $this->callSession($session);

        // 会话需要退出.
        if ($session->isQuiting()) {
            $this->forgetSession($chatId);
        }

        // 结束会话, 存储进度并回收垃圾.
        $session->finish();

        return $next($conversation);
    }


    public function callSession(Session $session) : Session
    {
        // 不走管道的话, 省一点代码运行量
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

    protected function getDestination() : \Closure
    {
        return function(Session $session) {
            return $this->doCallSession($session);
        };
    }

    protected function doCallSession(Session $session) : Session
    {
        /**
         * @var Ghost $host
         */
        $host = $session->conversation->make(Ghost::class);
        return $host->syncResponse();
    }

    /**
     * 获取 SessionId.
     * SessionId 通常由 CommuneChatbot 自己存取, 但也可能是从平台传入的.
     *
     * @param Conversation $conversation
     * @param string $chatId
     * @return Session\SessionId
     */
    public function fetchSessionId(
        Conversation $conversation,
        string $chatId
    ) : Session\SessionId
    {
        $sessionId = $conversation->getRequest()->fetchSessionId();

        if (isset($sessionId)) {
            return new Session\SessionId($chatId, $sessionId);
        }

        $key = $this->toSessionIdKey($chatId);

        $sessionId = $this->cache->get($key);
        if (empty($sessionId)) {
            $sessionId = $this->createUuId();
            $this->cache->set(
                $key,
                $sessionId,
                $this->hostConfig->sessionExpireSeconds
            );
        }

        return new Session\SessionId($chatId, $sessionId);
    }

    public function forgetSession(string $belongsTo) : void
    {
        $key = $this->toSessionIdKey($belongsTo);
        $this->cache->forget($key);
    }

    protected function toSessionIdKey(string $belongsTo) : string
    {
        return CacheKey::toSessionIdKey($belongsTo);
    }


    protected function makeSession(
        Conversation $conversation
    ) : Session
    {
        return $conversation->make(Session::class);
    }




    public function onFinally(Conversation $conversation): void
    {
    }


}