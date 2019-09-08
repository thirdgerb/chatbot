<?php

/**
 * Class QuitEvt
 * @package Commune\Chatbot\App\Messages\Events
 */

namespace Commune\Chatbot\App\Messages\Events;

use Commune\Chatbot\Blueprint\Message\Event\EndSession;
use Commune\Chatbot\Framework\Messages\AbsEventMsg;

class QuitEvt extends AbsEventMsg implements EndSession
{
}