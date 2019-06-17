<?php


namespace Commune\Chatbot\OOHost\Session;


use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Interface SessionLogger
 * @package Commune\Chatbot\Host\Session
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 为 session 定制的 logger, 主要是加入了一些 context
 */
class SessionLogger implements LoggerInterface
{
    use LoggerTrait;

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
    }


    public function log($level, $message, array $context = array())
    {
        $context = $context + [
                'sessionId' => $this->sessionId,
            ];
        $this->logger->log($level, $message, $context);
    }

    public function __destruct()
    {
        if (CHATBOT_DEBUG) {
            $this->logger->debug(__METHOD__);
        }
    }

}