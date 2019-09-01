<?php


namespace Commune\Demo\App\Listeners;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Framework\Events\ChatbotPipeClose;
use Psr\Log\LoggerInterface;

class PipeCloseListener
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected $traceId;

    /**
     * PipeCloseListener constructor.
     * @param Conversation $conversation
     */
    public function __construct(Conversation $conversation)
    {
        $this->traceId = $conversation->getTraceId();
        $this->logger = $conversation->getLogger();
    }


    public function handle(ChatbotPipeClose $event)
    {
        var_dump($event->pipe->getPipeName());
        $this->logger->info('triggerEvent ' . $event->pipe->getPipeName() . ' close of ' . $this->traceId);

    }

}