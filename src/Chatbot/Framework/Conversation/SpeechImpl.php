<?php

/**
 * Class Monologue
 * @package Commune\Chatbot\Framework\Conversation
 */

namespace Commune\Chatbot\Framework\Conversation;

use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Framework\Messages\Reply;
use Commune\Chatbot\Framework\Messages\Verbose;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Blueprint\Conversation\MessageRequest;
use Illuminate\Support\Collection;

class SpeechImpl implements Speech
{
    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * @var MessageRequest
     */
    protected $request;

    /**
     * @var Collection
     */
    protected $defaultSlots;

    /**
     * Monologue constructor.
     * @param Conversation $conversation
     * @param MessageRequest $request
     */
    public function __construct(
        Conversation $conversation,
        MessageRequest $request
    )
    {
        $this->conversation = $conversation;
        $this->request = $request;
    }

    public function error(string $message, array $slots = array()) : Speech
    {
        $this->log(Verbose::ERROR, $message, $slots);
        return $this;
    }

    public function warning(string $message, array $slots = array()) : Speech
    {
        $this->log(Verbose::WARN, $message, $slots);
        return $this;
    }

    public function notice(string $message, array $slots = array()) : Speech
    {
        $this->log(Verbose::NOTICE, $message, $slots);
        return $this;
    }

    public function info(string $message, array $slots = array()) : Speech
    {
        $this->log(Verbose::INFO, $message, $slots);
        return $this;
    }

    public function debug(string $message, array $slots = array()) : Speech
    {
        $this->log(Verbose::DEBUG, $message, $slots);
        return $this;
    }

    public function say( string $message, array $slots = []): Speech
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

    public function log(string $level, string $message, array $slots = array()) : Speech
    {
        $this->conversation->reply(new Reply($message, new Collection($slots), $level));
        return $this;
    }


}