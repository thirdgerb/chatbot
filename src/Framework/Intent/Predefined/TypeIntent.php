<?php

/**
 * Class TypeIntent
 * @package Commune\Chatbot\Framework\Intent\Predifined
 */

namespace Commune\Chatbot\Framework\Intent\Predefined;

use Commune\Chatbot\Framework\Message\Message;

class TypeIntent extends ArrayIntent
{

    public function __construct(Message $message)
    {
        parent::__construct(get_class($message), $message);
    }

}