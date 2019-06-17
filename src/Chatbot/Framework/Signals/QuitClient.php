<?php


namespace Commune\Chatbot\Framework\Signals;


use Commune\Chatbot\Blueprint\Pipeline\InitialPipe;
use Commune\Chatbot\Framework\Conversation\AbsSignal;

/**
 * @deprecated
 */
class QuitClient extends AbsSignal
{
    protected $pipeCloseListeners = [
        InitialPipe::class => 'closeServer'
    ];

    protected function closeServer(InitialPipe $pipe) : void
    {
        $bye = $this->conversation
            ->getChatbotConfig()
            ->defaultMessages
            ->farewell;

        $this->conversation
            ->monolog()
            ->info($bye);

        $this->conversation->finish();
    }
}