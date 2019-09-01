<?php


namespace Commune\Chatbot\Framework\Events;


use Commune\Chatbot\Blueprint\Pipeline\ChatbotPipe;

abstract class ChatbotPipeEvent
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