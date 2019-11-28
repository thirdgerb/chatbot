<?php

/**
 * Class EndSession
 * @package Commune\Chatbot\Blueprint\Message\Event
 */

namespace Commune\Chatbot\Blueprint\Message\Event;


use Commune\Chatbot\Blueprint\Message\EventMsg;

/**
 * 结束 session 的事件, 通常对应 dialog::quit
 */
interface EndSession extends EventMsg
{
}