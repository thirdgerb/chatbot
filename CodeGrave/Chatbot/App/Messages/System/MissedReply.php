<?php


namespace Commune\Chatbot\App\Messages\System;

use Commune\Chatbot\Blueprint\Conversation\Renderer;
use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Framework\Messages\AbsReply;

class MissedReply extends AbsReply
{
    public function __construct()
    {
        parent::__construct(
            Renderer::MISSED_ID,
            null,
            Speech::WARNING
        );
    }

    public static function mock()
    {
        return new static();
    }
}