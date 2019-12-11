<?php


namespace Commune\Chatbot\Framework\Conversation;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\Signal;
use Commune\Chatbot\Framework\Events\ChatbotPipeClose;
use Commune\Chatbot\Framework\Events\ChatbotPipeEvent;
use Commune\Chatbot\Framework\Events\ChatbotPipeStart;

/**
 * 信号机制. 暂时不用了, 思路有变化.
 *
 * @deprecated
 */
abstract class AbsSignal implements Signal
{

    protected $pipeStartListeners = [
        //clazz => func
    ];

    protected $pipeCloseListeners = [
        //clazz => func
    ];

    /**
     * @var Conversation
     */
    protected $conversation;

    public function withConversation(Conversation $conversation): Signal
    {
        $this->conversation = $conversation;
        return $this;
    }


    public function handle(ChatbotPipeEvent $event): void
    {
        if ($event instanceof ChatbotPipeStart) {
            $this->pipeCheck($this->pipeStartListeners, $event);
        }

        if ($event instanceof ChatbotPipeClose) {
            $this->pipeCheck($this->pipeCloseListeners, $event);
        }
    }

    protected function pipeCheck(array $funcs, ChatbotPipeEvent $event)
    {
        foreach ($funcs as $clazz => $func) {
            if (is_a($event->pipe, $clazz, TRUE)) {
                $break = call_user_func([$this, $func], $event->pipe);
                if ($break === true) continue;
            }
        }
    }

    public function __get($name)
    {
        return $this->{$name};
    }


}