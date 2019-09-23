<?php


namespace Commune\Chatbot\OOHost;


use Closure;
use Commune\Chatbot\App\Messages\System\MissedReply;
use Commune\Chatbot\App\Messages\System\QuitSessionReply;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Config\Children\OOHostConfig;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Framework\Pipeline\ChatbotPipeImpl;
use Commune\Chatbot\Framework\Utils\OnionPipeline;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * 面向对象的 host 机器人提供的chatbot pipe
 */
class OOHostPipe extends ChatbotPipeImpl implements HasIdGenerator
{
    use IdGeneratorHelper;

    /**
     * @var OOHostConfig
     */
    public $hostConfig;

    public $chatbotConfig;

    public function __construct(ChatbotConfig $config)
    {
        $this->chatbotConfig = $config;
        $this->hostConfig = $config->host;
    }


    public function handleUserMessage(Conversation $conversation, Closure $next): Conversation
    {
        // 用 sessionId 来唤醒一个session.
        $belongsTo = $conversation->getIncomingMessage()->getSessionId()
            ?? $conversation->getChat()->getChatId();

        $session = $this->makeSession($belongsTo, $conversation);

        $session = $this->callSession($session);

        $session->finish();

        // should close client by event
        if ($session->isQuiting()) {
            $conversation->reply(new QuitSessionReply());
            return $conversation;
        }

        // 当前 session 没有搞定, 就继续往下走.
        if (!$session->isHandled()) {
            $conversation = $next($conversation);
        }

        $replies = $conversation->getReplies();
        if (empty($replies)) {
            $conversation->reply(new MissedReply());
        }

        return $conversation;

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
        $session->handle($session->incomingMessage->message);
        return $session;
    }

    
    public function getDestination() : Closure
    {
        return function(Session $session) {
            return $this->doCallSession($session);
        };
    }

    public function makeSession(
        string $belongsTo,
        Conversation $conversation
    ) : Session
    {
        return $conversation->make(
            Session::class,
            [

                Session::BELONGS_TO_VAR => $belongsTo
            ]
        );

    }

    public function onUserMessageFinally(Conversation $conversation): void
    {
    }


}