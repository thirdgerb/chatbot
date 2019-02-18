<?php

/**
 * Class HostDriver
 * @package Commune\Chatbot\Framework
 */

namespace Commune\Chatbot\Framework;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Contracts\SessionDriver;
use Commune\Chatbot\Framework\Session\Session;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Directing\Director;
use Commune\Chatbot\Framework\Routing\Router;
use Psr\Log\LoggerInterface;

class HostDriver
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


    public function getSession(Conversation $conversation): Session
    {
        return new Session(
            $this->app,
            $this->sessionDriver,
            $this->log,
            $this->router,
            $conversation
        );
    }

    public function getDirector(Session $session): Director
    {
        return new Director(
            $this->app,
            $session,
            $this->router
        );
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getApp() : ChatbotApp
    {
        return $this->app;
    }



}