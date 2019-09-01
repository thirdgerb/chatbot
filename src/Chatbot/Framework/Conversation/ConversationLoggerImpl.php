<?php


namespace Commune\Chatbot\Framework\Conversation;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ConversationLogger;
use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class ConversationLoggerImpl implements ConversationLogger, RunningSpy
{
    use LoggerTrait, RunningSpyTrait;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string;
     */
    protected $conversationId;

    /**
     * @var string
     */
    protected $traceId;

    /**
     * @var bool
     */
    protected $isInstanced;

    /**
     * ConversationLogger constructor.
     * @param LoggerInterface $logger
     * @param Conversation $conversation
     */
    public function __construct(LoggerInterface $logger, Conversation $conversation)
    {
        $this->logger = $logger;
        $this->isInstanced = $conversation->isInstanced();
        if ($this->isInstanced) {
            $this->conversationId = $conversation->getConversationId();
            $this->traceId = $conversation->getTraceId();
            static::addRunningTrace($this->traceId, $this->traceId);
        }
    }

    public function log($level, $message, array $context = array())
    {
        if ($this->isInstanced) {
            $context['traceId'] = $this->traceId;
            $context['conversationId'] = $this->conversationId;
        }
        $this->logger->log($level, $message, $context);
    }


    public function __destruct()
    {
        if ($this->traceId) {
            static::removeRunningTrace($this->traceId);
        }
    }
}