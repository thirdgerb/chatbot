<?php

/**
 * Class Matcher
 * @package Commune\Chatbot\Framework\Intent\Matcher
 */

namespace Commune\Chatbot\Framework\Intent\Matcher;


use Commune\Chatbot\Framework\Message\Message;

abstract class Matcher
{

    abstract public function match(Message $message) : ? array ;

    public function __invoke()
    {
        $args = func_get_args();
        return call_user_func_array([$this, 'match'] ,$args);
    }
}