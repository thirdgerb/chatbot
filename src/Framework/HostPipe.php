<?php

/**
 * Class Host
 * @package Commune\Chatbot\Host
 */

namespace Commune\Chatbot\Framework;


use Commune\Chatbot\Framework\Exceptions\ConversationException;
use Commune\Chatbot\Framework\Exceptions\ChatbotHostException;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Psr\Log\LoggerInterface;

class HostPipe implements ChatbotPipe
{
    /**
     * @var HostDriver
     */
    protected $driver;

    /**
     * @var LoggerInterface
     */
    protected $log;

    public function __construct(
        HostDriver $driver,
        LoggerInterface $log
    )
    {
        $this->log = $log;
        $this->driver = $driver;
    }


    public function handle(Conversation $conversation, \Closure $next) : Conversation
    {
        $session = $this->driver->getSession($conversation);

        try {

            $director = $this->driver->getDirector($session);

            $conversation = $director->dispatch();

            $session->save();

            return $next($conversation);

        } catch (ConversationException $e) {

            return $e->getConversation();

        } catch (\Exception $e) {
            //todo
            $this->log->error('host error');
            throw new ChatbotHostException(get_class($e) . ':' . $e->getMessage(), 0, $e);

        } finally {
            $session->save();
        }
    }





}