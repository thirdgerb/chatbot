<?php

/**
 * Class StartEvt
 * @package Commune\Chatbot\App\Messages\Events
 */

namespace Commune\Chatbot\App\Messages\Events;


use Commune\Chatbot\Blueprint\Message\Event\StartSession;
use Commune\Chatbot\Framework\Messages\AbsEvent;

class StartEvt extends AbsEvent implements StartSession
{
}