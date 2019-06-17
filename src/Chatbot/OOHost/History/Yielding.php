<?php


namespace Commune\Chatbot\OOHost\History;

class Yielding
{

    /**
     * @var Thread
     */
    public $thread;

    /**
     * @var string
     */
    public $contextId;

    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
        $this->contextId = $thread->currentNode()->getContextId();
    }


}