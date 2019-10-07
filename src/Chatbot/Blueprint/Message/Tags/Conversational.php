<?php


namespace Commune\Chatbot\Blueprint\Message\Tags;


/**
 * 可对话的.
 */
interface Conversational
{
    /**
     * @return string[]
     */
    public function getSuggestions(): array;

}