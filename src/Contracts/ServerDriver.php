<?php

/**
 * Class ServerDriver
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;


use Commune\Chatbot\Framework\Character\Platform;
use Commune\Chatbot\Framework\Character\Recipient;
use Commune\Chatbot\Framework\Character\User;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Message\Message;

interface ServerDriver
{

    public function fetchSender($request) : User;

    public function fetchRecipient($request) : Recipient;

    public function fetchMessage($request) : Message;

    public function reply(Conversation $conversation);

    public function error(\Exception $e);

    public function getPlatform() : Platform;

    public function close();
}