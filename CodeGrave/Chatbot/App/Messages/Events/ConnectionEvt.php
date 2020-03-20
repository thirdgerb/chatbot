<?php


namespace Commune\Chatbot\App\Messages\Events;


use Commune\Chatbot\Blueprint\Message\Event\Connect;
use Commune\Chatbot\Framework\Messages\AbsEvent;

/**
 * 系统连接的事件.
 */
class ConnectionEvt extends AbsEvent implements Connect
{
}