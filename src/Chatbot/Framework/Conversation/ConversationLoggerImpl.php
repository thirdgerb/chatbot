<?php


namespace Commune\Chatbot\Framework\Conversation;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ConversationLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class ConversationLoggerImpl implements ConversationLogger
{
    use LoggerTrait;

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
        if (CHATBOT_DEBUG) {
            $this->logger->debug(__METHOD__);
        }
    }
}