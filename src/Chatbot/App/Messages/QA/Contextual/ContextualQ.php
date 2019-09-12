<?php

/**
 * Class ContextualQ
 * @package Commune\Chatbot\App\Messages\QA\Contextual
 */

namespace Commune\Chatbot\App\Messages\QA\Contextual;


use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Session\SessionDataIdentity;

interface ContextualQ
{

    public function getIntent(): ? IntentMessage;

    public function getIntentDataId(): SessionDataIdentity;

    public function getEntityName() : ? string;

    /**
     * @return string
     */
    public function getIntentName(): string;
}