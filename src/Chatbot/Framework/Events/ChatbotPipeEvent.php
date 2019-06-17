<?php


namespace Commune\Chatbot\Framework\Events;


use Commune\Chatbot\Blueprint\Pipeline\ChatbotPipe;
use Symfony\Component\EventDispatcher\Event;

abstract class ChatbotPipeEvent extends Event
{
    /**
     * @var ChatbotPipe
     */
    public $pipe;

    /**
     * ChatbotPipeClose constructor.
     * @param ChatbotPipe $pipe
     */
    public function __construct(ChatbotPipe $pipe)
    {
        $this->pipe = $pipe;
    }


}