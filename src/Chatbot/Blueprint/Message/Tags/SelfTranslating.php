<?php


namespace Commune\Chatbot\Blueprint\Message\Tags;


use Commune\Chatbot\Contracts\Translator;

interface SelfTranslating
{
    /**
     * @param Translator $translator
     */
    public function translateBy(Translator $translator) : void;

}