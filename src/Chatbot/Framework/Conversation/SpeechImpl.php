<?php

/**
 * Class Monologue
 * @package Commune\Chatbot\Framework\Conversation
 */

namespace Commune\Chatbot\Framework\Conversation;

use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Framework\Messages\Reply;
use Commune\Chatbot\Blueprint\Message\VerboseMsg as Verbose;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\Speech;
use Illuminate\Support\Collection;

class SpeechImpl implements Speech
{
    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * @var Collection
     */
    protected $defaultSlots;

    /**
     * Monologue constructor.
     * @param Conversation $conversation
     */
    public function __construct(
        Conversation $conversation
    )
    {
        $this->conversation = $conversation;
    }

    public function error(string $message, array $slots = array()) 
    {
        $this->log(Verbose::ERROR, $message, $slots);
        return $this;
    }

    public function warning(string $message, array $slots = array()) 
    {
        $this->log(Verbose::WARN, $message, $slots);
        return $this;
    }

    public function notice(string $message, array $slots = array()) 
    {
        $this->log(Verbose::NOTICE, $message, $slots);
        return $this;
    }

    public function info(string $message, array $slots = array()) 
    {
        $this->log(Verbose::INFO, $message, $slots);
        return $this;
    }

    public function debug(string $message, array $slots = array()) 
    {
        $this->log(Verbose::DEBUG, $message, $slots);
        return $this;
    }

    public function say( string $message, array $slots = [])
    {
        $this->info($message, $slots );
        return $this;
    }

    public function trans(string $id, array $slots = []): string
    {
        /**
         * @var Translator $trans
         */
        $trans = $this->conversation->make(Translator::class);
        return $trans->trans($id, $slots, Translator::MESSAGE_DOMAIN, null);
    }

    public function log(string $level, string $message, array $slots = array()) : void
    {
        $this->conversation->reply(new Reply($message, new Collection($slots), $level));
    }
}
