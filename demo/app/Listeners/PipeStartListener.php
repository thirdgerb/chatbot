<?php


namespace Commune\Demo\App\Listeners;


use Commune\Chatbot\Framework\Events\ChatbotPipeStart;
use Psr\Log\LoggerInterface;

class PipeStartListener
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * PipeStartListener constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    public function handle(ChatbotPipeStart $event)
    {
        $this->logger->info('triggerEvent ' . $event->pipe->getPipeName() . ' start');

    }

}