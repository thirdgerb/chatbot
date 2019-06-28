<?php


namespace Commune\Chatbot\App\Components\Rasa;


use Commune\Chatbot\App\Components\RasaComponent;
use Commune\Chatbot\OOHost\NLU\NatureLanguageUnit;
use Commune\Chatbot\OOHost\Session\SessionPipe;

interface RasaNLUPipe extends NatureLanguageUnit, SessionPipe
{
    public function getConfig() : RasaComponent;
}