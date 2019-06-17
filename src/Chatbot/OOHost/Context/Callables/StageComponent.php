<?php


namespace Commune\Chatbot\OOHost\Context\Callables;


use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;

interface StageComponent
{

    public function __invoke(Stage $stage) : Navigator;
}