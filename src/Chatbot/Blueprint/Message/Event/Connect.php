<?php

/**
 * Class Connect
 * @package Commune\Chatbot\Blueprint\Message\Event
 */

namespace Commune\Chatbot\Blueprint\Message\Event;


use Commune\Chatbot\Blueprint\Message\EventMsg;

/**
 * 表示会话连接的事件. 通常对应 dialog::repeat 方法
 */
interface Connect extends EventMsg
{
}