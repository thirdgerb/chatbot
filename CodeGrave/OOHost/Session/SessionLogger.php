<?php


namespace Commune\Chatbot\OOHost\Session;


use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Interface SessionLogger
 * @package Commune\Chatbot\Host\Session
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 为 session 定制的 logger, 主要是加入了一些 context
 */
class SessionLogger implements LoggerInterface, RunningSpy
{
    use LoggerTrait, RunningSpyTrait;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * SessionLogger constructor.
     * @param LoggerInterface $logger
     * @param Session $session
     */
    public function __construct(LoggerInterface $logger, Session $session)
    {
        $this->logger = $logger;
        $this->sessionId = $session->sessionId;

        static::addRunningTrace($this->sessionId, $this->sessionId);
    }


    public function log($level, $message, array $context = array())
    {
        $message = (string) $message;
        $context = $context + [
            'sessionId' => $this->sessionId,
        ];
        $this->logger->log($level, $message, $context);
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->sessionId);
    }

}