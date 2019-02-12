<?php

/**
 * Class Application
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;


interface ChatbotKernel
{
    public function handle($request);
}