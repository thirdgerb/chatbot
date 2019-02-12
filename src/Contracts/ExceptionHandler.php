<?php

/**
 * Class ExceptionHandler
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;

interface ExceptionHandler
{


    public function handle(\Exception $e);

}