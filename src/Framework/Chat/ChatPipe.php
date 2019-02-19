<?php

/**
 * Class Chat
 * @package Commune\Chatbot\Chat
 */

namespace Commune\Chatbot\Framework\Chat;

use Commune\Chatbot\Framework\ChatbotPipe;
use Commune\Chatbot\Contracts\ChatDriver;
use Commune\Chatbot\Contracts\IdGenerator;
use Psr\Log\LoggerInterface;
use Commune\Chatbot\Framework\Exceptions\ChatbotPipeException;
use Commune\Chatbot\Framework\Exceptions\TooBusyException;
use Commune\Chatbot\Framework\Conversation\Conversation;

class ChatPipe implements ChatbotPipe
{
    /**
     * @var ChatDriver
     */
    protected $driver;

    /**
     * @var IdGenerator
     */
    protected $idGenerator;

    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * ChatPipe constructor.
     * @param ChatDriver $driver
     * @param IdGenerator $idGenerator
     * @param LoggerInterface $log
     */
    public function __construct(ChatDriver $driver, IdGenerator $idGenerator, LoggerInterface $log)
    {
        $this->driver = $driver;
        $this->idGenerator = $idGenerator;
        $this->log = $log;
    }


    public function handle(Conversation $conversation, \Closure $next) : Conversation
    {
        $chatId = $this->driver->fetchIdOrCreateChat($conversation);

        $conversation = $this->completeConversation($conversation);

        $this->driver->pushIncomingMessage(
            $chatId,
            $conversation->getSessionId(),
            $conversation->getIncomingMessage()
        );

        if ($this->driver->chatIsTooBusy($chatId)) {
            $conversation->reply($this->driver->replyWhenTooBusy());
            return $conversation;
        }

        if (!$this->driver->lockChat($chatId)) {
            return $conversation;
        }

        $incomingMessage = null;

        try {
            while ($incomingMessage = $this->driver->popIncomingMessage($chatId)) {

                $newConversation = new Conversation(
                    $incomingMessage,
                    $chatId,
                    $conversation->getSessionId()
                );
                $newConversation->isCloseSession();

                /**
                 * @var Conversation $replyConversation
                 */
                $replyConversation = $next($newConversation);

                $this->driver->saveReplies($replyConversation);
                $conversation->mergeReplies($replyConversation);

                if ($replyConversation->isCloseSession()) {
                    $conversation->closeSession();
                    $this->driver->closeSession($conversation->getChatId());
                    return $conversation;
                }
            }

            $this->driver->unlockChat($chatId);

            return $conversation;

        } catch (TooBusyException $e) {

            $this->driver->flushAwaitIncomingMessages($chatId);
            $conversation->reply($this->driver->replyWhenTooBusy());
            $this->driver->unlockChat($chatId);
            return $conversation;

        } catch (ChatbotPipeException $e) {

            $this->driver->closeSession($chatId);
            $this->driver->unlockChat($chatId);
            throw $e;

        } catch (\Exception $e) {
            //todo
            $message = "chat bot failure";
            $this->driver->closeSession($chatId);
            $this->driver->unlockChat($chatId);

            throw new ChatbotPipeException($message, null, $e);
        }
    }

    protected function completeConversation(Conversation $conversation)
    {
        $chatId = $conversation->getChatId();
        $sessionId = $this->driver->fetchSessionIdOfChat($chatId);
        $conversation->setSessionId($sessionId);

        return $conversation;
    }

}