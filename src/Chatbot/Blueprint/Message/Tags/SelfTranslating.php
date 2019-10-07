<?php


namespace Commune\Chatbot\Blueprint\Message\Tags;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Contracts\Translator;

interface SelfTranslating
{
    /**
     * @param Translator $translator
     * @return Message[]
     */
    public function translateBy(Translator $translator) : array;

}