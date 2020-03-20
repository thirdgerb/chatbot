<?php

/**
 * Class StartSession
 * @package Commune\Chatbot\Blueprint\Message\Event
 */

namespace Commune\Chatbot\Blueprint\Message\Event;


use Commune\Chatbot\Blueprint\Message\EventMsg;

/**
 * 开启一个 session 的事件, 通常对应 dialog::home
 */
interface StartSession extends EventMsg
{
}