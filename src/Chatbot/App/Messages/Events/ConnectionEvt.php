<?php


namespace Commune\Chatbot\App\Messages\Events;


use Commune\Chatbot\Blueprint\Message\Event\Connect;
use Commune\Chatbot\Framework\Messages\AbsEventMsg;

/**
 * 系统连接的事件.
 */
class ConnectionEvt extends AbsEventMsg implements Connect
{
}