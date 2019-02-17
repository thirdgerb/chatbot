<?php

/**
 * Class Runner
 * @package Commune\Chatbot\Framework\Routing\Runners
 */

namespace Commune\Chatbot\Framework\Routing\Runners;


use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Directing\Director;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Intent\Intent;

abstract class Runner
{

    abstract public function run(Director $director, Context $context, Intent $intent) : Conversation;


}