<?php

/**
 * Class Host
 * @package Commune\Chatbot\Host
 */

namespace Commune\Chatbot\Framework;


use Commune\Chatbot\Framework\Exceptions\ChatbotException;
use Commune\Chatbot\Framework\Exceptions\ChatbotPipeException;
use Commune\Chatbot\Framework\Exceptions\ConversationException;
use Commune\Chatbot\Framework\Exceptions\ChatbotHostException;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Exceptions\HostPipeException;
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
        } catch (ChatbotPipeException $e) {
            //透传
            throw $e;

        } catch (\Exception $e) {
            //todo
            throw new HostPipeException('host pipe unexpected exception : ' . $e->getMessage(), 0, $e);

        } finally {
            $session->save();
        }
    }





}