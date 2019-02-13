<?php

/**
 * Class Host
 * @package Commune\Chatbot\Host
 */

namespace Commune\Chatbot\Framework;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Contracts\SessionDriver;
use Commune\Chatbot\Framework\Exceptions\ConversationException;
use Commune\Chatbot\Framework\Exceptions\HostException;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Directing\Director;
use Commune\Chatbot\Framework\Routing\Router;
use Commune\Chatbot\Framework\Session\Session;
use Psr\Log\LoggerInterface;

class HostPipe implements ChatbotPipe
{
    /**
     * @var ChatbotApp
     */
    protected $app;

    protected $router;

    protected $sessionDriver;

    /**
     * @var LoggerInterface
     */
    protected $log;

    public function __construct(
        ChatbotApp $app,
        Router $router,
        SessionDriver $driver,
        LoggerInterface $log
    )
    {
        $this->app = $app;
        $this->router = $router;
        $this->sessionDriver = $driver;
        $this->log = $log;
    }


    public function handle(Conversation $conversation, \Closure $next) : Conversation
    {

        $session = $this->makeSession($conversation);

        try {

            $director = new Director($this->app, $session, $this->router);

            $conversation = $director->dispatch();

            $session->save();

            return $next($conversation);

        } catch (ConversationException $e) {

            return $e->getConversation();

        } catch (\Exception $e) {
            //todo
            $this->log->error('host error');
            throw new HostException('test', 0, $e);

        } finally {
            $session->save();
        }
    }


    protected function router() : Router
    {
        return $this->app->make(Router::class);
    }

    protected function makeSession(Conversation $conversation) : Session
    {
        return new Session(
            $this->app,
            $this->sessionDriver,
            $this->log,
            $this->router,
            $conversation
        );
    }




}