<?php


namespace Commune\Chatbot\Blueprint\Message\Tags;


/**
 * 拥有 suggestions 的消息, 因此构成有选项的对话.
 */
interface Conversational
{
    /**
     * @return string[]
     */
    public function getSuggestions(): array;

}