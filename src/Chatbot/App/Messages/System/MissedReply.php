<?php


namespace Commune\Chatbot\App\Messages\System;

use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Framework\Messages\AbsReply;

class MissedReply extends AbsReply
{
    const REPLY_ID = 'system.miss';

    public function __construct()
    {
        parent::__construct(self::REPLY_ID, null, Speech::WARNING);
    }

    public static function mock()
    {
        return new static();
    }
}