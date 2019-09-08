<?php


namespace Commune\Chatbot\Blueprint\Message\Tags;


use Commune\Chatbot\Contracts\Translator;

interface SelfTranslating
{
    public function translateBy(Translator $translator) : void;

}