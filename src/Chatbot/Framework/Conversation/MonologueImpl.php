<?php

/**
 * Class Monologue
 * @package Commune\Chatbot\Framework\Conversation
 */

namespace Commune\Chatbot\Framework\Conversation;

use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Framework\Messages\Verbose;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\Monologue;
use Commune\Chatbot\Blueprint\Conversation\MessageRequest;

class MonologueImpl implements Monologue
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

    public function error(string $message, array $slots = array()) : void
    {
        $this->log(Verbose::ERROR, $message, $slots);
    }

    public function warning(string $message, array $slots = array()) : void
    {
        $this->log(Verbose::WARN, $message, $slots);
    }

    public function notice(string $message, array $slots = array()) : void
    {
        $this->log(Verbose::NOTICE, $message, $slots);
    }

    public function info(string $message, array $slots = array()) : void
    {
        $this->log(Verbose::INFO, $message, $slots);
    }

    public function debug(string $message, array $slots = array()) : void
    {
        $this->log(Verbose::DEBUG, $message, $slots);
    }

    public function say( string $message, array $slots = []): void
    {
        $this->info($message, $slots);
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
        $this->conversation->reply(
            (new Verbose($message))
                ->withSlots($slots)
                ->withLevel($level)
        );
    }


}