<?php

/**
 * Class Talkable
 * @package Commune\Chatbot\Host\Conversation
 */

namespace Commune\Chatbot\Framework\Conversation;

use Commune\Chatbot\Framework\Message\Message;

interface Talkable
{

    public function say(string $text, int $style, string $verbose = Message::NORMAL);

    public function info(string $message, string $verbose = Message::NORMAL);

    public function warn(string $message, string $verbose = Message::NORMAL);

    public function error(string $message, string $verbose = Message::NORMAL);

    public function reply(Message $message);

}