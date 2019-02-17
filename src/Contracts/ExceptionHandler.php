<?php

/**
 * Class ExceptionHandler
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;

use Commune\Chatbot\Framework\Exceptions\ChatbotException;
use Commune\Chatbot\Framework\Message\Message;

interface ExceptionHandler
{

    public function handle(\Exception $e);

    public function render(ChatbotException $e) : Message;

}